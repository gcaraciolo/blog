# DBIndex

## timezone
```sql
--SET TIME ZONE 'UTC';
--select now();
```

## explain analyse
```sql
explain analyse
select * 
from "events" 
where "status" = 'scheduled' 
and "launch_time" <= now()
order by "launch_time" desc 
limit 100;
```
