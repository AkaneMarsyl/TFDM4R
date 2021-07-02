<?php

/**
 * Tiny Flatfile Database Manager For Retards.
 * Usage:
 * - Create an empty file next to this script, this is your database flatfile.
 * - $ff = new Flatfile('database');
 * - $ff->create('table');
 * - $ff->query('INSERT INTO table', ['foo'=>'bar']); (A variable called 'id' will be generated and will automatically increment).
 * - $query = $ff->query('SELECT foo|ALL FROM table WHERE id = 1');
 * - $ff->query('UPDATE foo FROM table WHERE id = 1 VALUE new_bar');
 * - $ff->query('DELETE FROM table WHERE id = 1');
 * - $ff->save(); When you're done, write down all changes in the flatfile.
 * 
 * @author AkaneMarsyl
 */
class Flatfile
{
    protected $path;
    protected $database;
    protected $result;

    /**
     * @param string $database Path to the flatfile
     */
    public function __construct($database)
    {
        $this->path = $database;
        $this->database = unserialize(file_get_contents($database));
    }

    /**
     * @param string $table
     * @return Flatfile
     */
    public function create(string $table): Flatfile
    {
        $this->database[$table] = [];
        $this->database['sys'] = [$table => ['id' => 1]];
        return $this;
    }

    /**
     * @param string $table
     * @return Flatfile
     */
    public function delete($table): Flatfile
    {
        unset($this->database[$table]);
        return $this;
    }

    public function save()
    {
        file_put_contents($this->path, serialize($this->database));
    }

    /**
     * @param string $query
     * @param array $data Used with INSERT command.
     * @return array|null
     */
    public function query(string $query, ?array $data = null): ?array
    {
        
        if(!preg_match('/((DELETE) FROM (\w+) WHERE (\w+) (=|<|>) (.+)|(SELECT) (\w+) FROM (\w+)(?: WHERE (\w+) (=|<|>) (.+))?|(UPDATE) (\w+) FROM (\w+) WHERE (\w+) (=|<|>) (.+) VALUE (.+)|INSERT INTO (\w+))/', $query, $match)){
            throw new Exception('Wrong TFDM4R query');
        }
        
        if($match[2]){//DELETE
            $this->select($match[2], $match[3], $match[4], $match[5], $match[6], null);
        }elseif($match[7]){//SELECT
            $this->select($match[8], $match[9], $match[10] ?? null, $match[11] ?? null, $match[12] ?? null, null);
        }elseif($match[13]){//UPDATE
            $this->select($match[14], $match[15], $match[16], $match[17], $match[18], $match[19]);
        }elseif($match[20]){//INSERT
            $data['id'] = $this->database['sys'][$match[20]]['id'];
            $this->database['sys'][$match[20]]['id']++;
            $this->database[$match[20]][] = $data;
        }else{
            return false;
        }
        return $this->result ? array_values($this->result) : null;
    }

    private function select($element, $table, $column, $operator, $var, $new)
    {
        switch($operator){
            case '=':
                foreach($this->database[$table] as $key=>$item){
                    if($item[$column] == $var){
                        if($element == 'DELETE'){unset($this->database[$table][$key]);
                        }else{
                            $new ? $this->database[$table][$key][$element] = $new :
                            $this->result[$key] = $element == 'ALL' ? $this->database[$table][$key] : $this->database[$table][$key][$element];
                        }
                    }
                }
                break;
            case '<':
                foreach($this->database[$table] as $key=>$item){
                    if($item[$column] < $var){
                        if($element == 'DELETE'){unset($this->database[$table][$key]);
                        }else{
                            $new ? $this->database[$table][$key][$element] = $new :
                            $this->result[$key] = $element == 'ALL' ? $this->database[$table][$key] : $this->database[$table][$key][$element];
                        }
                    }
                }
                break;
            case '>':
                foreach($this->database[$table] as $key=>$item){
                    if($item[$column] > $var){
                        if($element == 'DELETE'){unset($this->database[$table][$key]);
                        }else{
                            $new ? $this->database[$table][$key][$element] = $new :
                            $this->result[$key] = $element == 'ALL' ? $this->database[$table][$key] : $this->database[$table][$key][$element];
                        }
                    }
                }
                break;
            default:
                $this->result = $this->database[$table];
            
        }
    }
}
