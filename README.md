tsfw-db
=======
A very easy to use database library which extends PDO with some nice and straight-ahead functions.

## Example
```php
$dbConnect = new DBConnect('localhost', 'my_db', 'my_db_user, 'secret');
$db = new DBMySQL($dbConnect);

$stmnt = $db->prepare("SELECT ID, name, email FROM user WHERE email LIKE ?");
$users = $db->select($stmnt, array('%@example.com%'));

foreach($users as $user) {
	echo $user->ID , ', ' , $user->name , '(' , $user->email , ')<br>';
}
```