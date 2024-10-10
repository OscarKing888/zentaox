        // oscar[  自动关联到工程
        $this->loadModel('pipeline');
        $projects = $this->dao->select('id, project')->from(TABLE_AUTOSTORY)
            ->where('product')->eq($productID)
            ->orderBy('project asc')
            ->fetchPairs();

        $this->loadModel('project');
        foreach ($projects as $k => $v)
        {
            error_log("oscar: batch link story ---- project:$k -> product:$v");
            $this->linkStory($v, $productID, $storiesDat);
        }
        // oscar]