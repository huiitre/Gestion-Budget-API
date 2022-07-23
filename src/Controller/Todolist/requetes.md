```sql
select * from subcategory s ;
select * from category c ;
select * from todolist t ;
select * from todo t where todolist_id = 2;
select * from `transaction` t ;

-- Liste des todolist
select t.*,
c.name as category,
(select count(t2.id) from todo t2 where todolist_id = t.id) as todos,
(select count(t2.id) from todo t2 where todolist_id = t.id and t2.percent != 100 ) as activesTodos,
(select count(t2.id) from todo t2 where todolist_id = t.id and t2.percent = 100) as donesTodos
from todolist t
inner join category c 
on t.category_id = c.id
where user_id = 1
order by t.created_at desc;

-- Liste des todos d'une liste
select t.*
from todo t 
where todolist_id = 1
order by created_at desc;
```