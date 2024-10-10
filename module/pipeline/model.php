<?php
/**
 * The model file of pipeline module of ZenTaoPHP.
 *
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 *
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */
?>
<?php

class pipelineModel extends model
{

    public function __construct()
    {
        parent::__construct();

        $this->loadModel('dept');
        $this->loadModel('file');
        $this->loadModel('user');
    }

    /**
     * Get article lists.
     *
     * @access public
     * @return array
     */
    public function getList($pager = null)
    {
        $pipelines = $this->dao->select('id,pipename')
            ->from(TABLE_PIPELINE)
            //->where('owner')->eq($this->app->user->account)
            ->where('deleted')->eq(0)
            ->orderBy('id asc')->page($pager)->fetchAll();


        foreach ($pipelines as $k => $val) {
            //error_log("pipeline:$val->id val:$val->pipename");

            $steps = $this->dao->select('*')
                ->from(TABLE_PIPELINE_STAGES)
                ->where('gamepipeline')->eq($val->id)
                ->orderBy('id asc')->fetchAll();
            $pipelines[$k]->steps = $steps;

            /*
            foreach ($steps as $step) {
                error_log(" step:$step->desc");
            }
            //*/
        }

        return $pipelines;
    }


    public function getDeletedList($pager = null)
    {
        $pipelines = $this->dao->select('*')
            ->from(TABLE_PIPELINE)
            //->where('owner')->eq($this->app->user->account)
            ->where('deleted')->eq(1)
            ->orderBy('id desc')->page($pager)->fetchAll();

        foreach ($pipelines as $k => $val) {
            //error_log("pipeline:$val->id val:$val->pipename");

            $steps = $this->dao->select('*')
                ->from(TABLE_PIPELINE_STAGES)
                ->where('gamepipeline')->eq($val->id)
                ->orderBy('id asc')->fetchAll();
            $pipelines[$k]->steps = $steps;

            /*
            foreach ($steps as $step) {
                error_log(" step:$step->desc");
            }
            //*/
        }

        return $pipelines;
    }

    /**
     * Get an article.
     *
     * @param  int $id
     * @access public
     * @return object
     */
    public function getById($id)
    {
        $pipeline = $this->dao->select('id,pipename')
            ->from(TABLE_PIPELINE)
            ->where('id')->eq($id)->fetch();

        $steps = $this->dao->select('*')
            ->from(TABLE_PIPELINE_STAGES)
            ->where('gamepipeline')->eq($pipeline->id)
            ->orderBy('id asc')->fetchAll();
        $pipeline->steps = $steps;
        return $pipeline;
        /*
        foreach ($steps as $step) {
            error_log(" edit pipeline step:$step->desc");
        }
        //*/
    }

    public function logpipeline($log)
    {
        if (!$this->config->pipeline->debug)
            return false;

        if (!is_writable($this->app->getLogRoot()))
            return false;

        $file = $this->app->getLogRoot() . 'pipeline.' . date('Ymd') . '.log.php';
        if (!is_file($file)) $log = "<?php\n die();\n" . $log . "\n";

        $fp = fopen($file, "a");
        fwrite($fp, $log);
        fclose($fp);
    }

    /**
     * Create an article.
     *
     * @access public
     * @return void
     */
    public function create()
    {
        $pst = fixer::input('post')
            ->get();

        /*
        foreach ($pst as $k => $v) {
            error_log("$k -> $v");
            if(is_array($v))
            {
                foreach($v as $kk => $vv)
                {
                    error_log(" $kk -> $vv");
                }
            }
        }
        //*/

        $pipeline = new stdClass();
        $pipeline->pipename = $pst->pipename;

        $r = $this->dao->select()->from(TABLE_PIPELINE)->where('pipename')->eq($pst->pipename);
        if (!empty($r)) {
            error_log("oscar: pipename already exist:$pst->pipename");
            //return;
        }

        $this->dao->insert(TABLE_PIPELINE)->data($pipeline)->autoCheck()->batchCheck($this->config->pipeline->create->requiredFields, 'notempty')->exec();
        if (!$this->dao->isError()) {
            $pipeId = $this->dao->lastInsertID();

            $parentStepID = 0;

            //error_log("============== add steps=============");

            $idx = 0;
            foreach ($pst->steps as $stepID => $dept) {
                //if ($dept == 0) continue;
                $stepType = $this->post->stepType;
                $step = new stdClass();
                $step->type = ($stepType[$stepID] == 'group' and $parentStepID == 0) ? 'group' : $stepType[$stepID];
                $step->parent = ($step->type == 'group') ? $parentStepID : 0;
                $step->gamepipeline = $pipeId;
                $step->desc = $stepID;

                $step->estimate = (int)$pst->expects[$stepID];
                if($step->type == 'step')
                {
                    $step->stepname = $dept;
                    $step->dept = 0;
                }
                else
                {
                    $step->stepname = "dept";
                    $step->dept = $dept;
                }

                //error_log("oscaar: ======= stepID:$stepID -> dept:$dept type:$step->type stepName:$step->stepname parent:$step->parent pipeId:$step->gamepipeline workhour:$step->estimate");

                if ($dept == 0 && $idx + 1 == count($pst->steps))
                {
                    break;
                }

                $this->dao->insert(TABLE_PIPELINE_STAGES)->data($step)->autoCheck()->exec();
                ++$idx;

                if ($step->type == 'group') $parentStepID = $this->dao->lastInsertID();
                if ($step->type == 'step') $parentStepID = 0;
            }

            //error_log("============== add steps end=============");
            //return array('status' => 'created', 'id' => $pipeId);
        }
    }


    /**
     * Update an article.
     *
     * @param  int $articleID
     * @access public
     * @return void
     */
    public function update($pipelineId)
    {
        $pst = fixer::input('post')
            ->get();

        $this->dao->update(TABLE_PIPELINE)
            ->set('pipename')->eq($pst->pipename)
            ->where('id')->eq($pipelineId)
            ->autoCheck()->batchCheck($this->config->pipeline->create->requiredFields, 'notempty')->exec();

        if (!$this->dao->isError()) {

            $this->dao->delete()->from(TABLE_PIPELINE_STAGES)
                ->where('gamepipeline')->eq($pipelineId)
                ->exec();

            $parentStepID = 0;
            $idx = 0;
            foreach ($pst->steps as $stepID => $dept) {
                //if ($dept == 0) continue;
                $stepType = $this->post->stepType;
                $step = new stdClass();
                $step->type = ($stepType[$stepID] == 'group' and $parentStepID == 0) ? 'group' : $stepType[$stepID];
                $step->parent = ($step->type == 'group') ? $parentStepID : 0;
                $step->gamepipeline = $pipelineId;
                $step->desc = $stepID;
                $step->dept = $dept;
                $step->estimate = (int)$pst->expects[$stepID];

                //error_log("oscar: ======= update stepID:$stepID -> dept:$dept type:$step->type parent:$step->parent pipeId:$step->gamepipeline workhour:$step->estimate");

                if($step->type == 'step')
                {
                    $step->stepname = $dept;
                    $step->dept = 0;
                }
                else
                {
                    $step->stepname = "dept";
                    $step->dept = $dept;
                }

                //error_log("oscar: ======= update stepID:$stepID -> dept:$dept type:$step->type parent:$step->parent pipeId:$step->gamepipeline workhour:$step->estimate");
                //error_log("oscar: ======= update stepID:$stepID -> dept:$dept type:$step->type stepName:$step->stepname parent:$step->parent pipeId:$step->gamepipeline workhour:$step->estimate");

                if ($dept == 0 && $idx + 1 == count($pst->steps))
                {
                    break;
                }

                $this->dao->insert(TABLE_PIPELINE_STAGES)->data($step)->autoCheck()->exec();
                ++$idx;

                if ($step->type == 'group') $parentStepID = $this->dao->lastInsertID();
                if ($step->type == 'step') $parentStepID = 0;
            }
            //return array('status' => 'created', 'id' => $pipeId);
        }
    }

    /**
     * Delete an article.
     *
     * @param  int $id
     * @param  null $table
     * @access public
     * @return void
     */
    public function delete($id)
    {
        $this->dao->update(TABLE_PIPELINE)
            ->set('deleted')->eq(1)->where('id')->eq($id)->exec();
    }

    public function restore($id, $table = null)
    {
        $this->dao->update(TABLE_PIPELINE)
            ->set('deleted')->eq(0)->where('id')->eq($id)->exec();
    }


    public function getOptionMenu()
    {
        $pipelines = $this->dao->select()
            ->from(TABLE_PIPELINE)
            ->where('deleted')->eq(0)
            ->orderBy('id asc')->fetchAll();

        $menu = array();

        foreach ($pipelines as $k => $val) {
            $menu[$val->id] = $val->pipename;
        }

        return $menu;
    }

    public function setupOptionMenu($view)
    {
        $view->pipeline = $this->getOptionMenu();
    }
}
