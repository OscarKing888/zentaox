SELECT *
FROM
  gameblog
WHERE
  owner IN (SELECT zt_user.account FROM zt_user WHERE zt_user.dept = 1) AND 
  gameblog.deleted <> 1

