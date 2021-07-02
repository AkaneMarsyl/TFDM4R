# TFDM4R
Tiny Flatfile Database Manager For Retards

A database manager for small projects that don't need to keep anything secret.

 Usage:
 <pre>
 * Create an empty file next to this script, this is your database flatfile.
 * $ff = new Flatfile('database');
 * $ff->create('table');
 * $ff->query('INSERT INTO table', ['foo'=>'bar']); (A variable called 'id' will be generated and will automatically increment).
 * $query = $ff->query('SELECT foo|ALL FROM table WHERE id = 1');
 * $ff->query('UPDATE foo FROM table WHERE id = 1 VALUE new_bar');
 * $ff->query('DELETE FROM table WHERE id = 1');
 * $ff->save(); When you're done, write down all changes in the flatfile.
 </pre>
