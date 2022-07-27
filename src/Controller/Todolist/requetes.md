select * from subcategory s ;

select * from category c ;

select * from todolist t ;
-- delete from todolist ;
select * from todo t where todolist_id = 15;

select * from `transaction` t ;

-- Liste des todolist
select t.*,
c.name as category
from todolist t
inner join category c 
on t.category_id = c.id
where user_id = 1
order by t.created_at desc;

-- Liste des todos d'une liste
select t.*
from todo t 
where todolist_id = 1
order by created_at asc;

-- Liste des todos d'une liste en fonction de l'user courant
select t.*
from todo t 
where todolist_id in (
	select t2.id 
	from todolist t2 
	where t2.user_id = 1
)
and todolist_id = 15

-- Insertion d'une todolist
insert into todolist 
(name, category_id, user_id, is_done, percent, created_at) values ("premiere liste", 4, 1, false, 0, now());

call createTodolist("ma première procédure bordel !!", 4, 1, null, null, null, null, null, null);

-- Suppression d'une todolist avec ces todo
delete from todo
where todolist_id = (
	select t2.id
	from todolist t2
	where t2.id = 1
	and t2.user_id = 3
);

call deleteTodolist(4, 2);

-- Création d'un todo
insert into todo 
(name, todolist_id, created_at, is_done, percent) values ('test 1', 50, now(), false, 0);


call createTodo('test dans dbeaver', 1, now(), false, 50, 15);

-- Suppression d'un todo d'une liste
delete from todo 
where todolist_id = (
	select t2.id
	from todolist t2
	where t2.id = 1
	and t2.user_id = 1
)
and id = 2;

call deleteTodo(9, 1, 69);


-- procédure qui recalcul une liste
call calculTodolistByTodo(15);
select id, all_todos, active_todos, done_todos from todolist t ;

-- Modification d'une liste
update todolist
set (name, category_id) values ('test 1 update', category)
where id = 124
and user_id = 1;
