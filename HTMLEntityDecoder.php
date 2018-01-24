
class HTMLEntityDecoder
{
    protected $filename;
    protected $data;
    
    public function __construct($filename){
        $this->filename= $filename;
    }
    
    protected function parse_format($data)
    {
        $info = array(
        );
        if (preg_match_all('
    @^\\s*                           # Start at the beginning of a line, ignoring leading whitespace
    ((?:
      [^=;\\[\\]]|                    # Key names cannot contain equal signs, semi-colons or square brackets,
      \\[[^\\[\\]]*\\]                  # unless they are balanced and not nested
    )+?)
    \\s*=\\s*                         # Key/value pairs are separated by equal signs (ignoring white-space)
    (?:
      ("(?:[^"]|(?<=\\\\)")*")|     # Double-quoted string, which may contain slash-escaped quotes/slashes
      (\'(?:[^\']|(?<=\\\\)\')*\')| # Single-quoted string, which may contain slash-escaped quotes/slashes
      ([^\\r\\n]*?)                   # Non-quoted string
    )\\s*$                           # Stop at the next end of a line, ignoring trailing whitespace
    @msx', $data, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                
                // Fetch the key and value string.
                $i = 0;
                foreach (array(
                    'key',
                    'value1',
                    'value2',
                    'value3'
                ) as $var) {
                    ${$var} = isset($match[++ $i]) ? $match[$i] : '';
                }
                $value = stripslashes(substr($value1, 1, - 1)) . stripslashes(substr($value2, 1, - 1)) . $value3;
                
                // Parse array syntax.
                $keys = preg_split('/\\]?\\[/', rtrim($key, ']'));
                $last = array_pop($keys);
                $parent = & $info;
                
                // Create nested arrays.
                foreach ($keys as $key) {
                    if ($key == '') {
                        $key = count($parent);
                    }
                    if (! isset($parent[$key]) || ! is_array($parent[$key])) {
                        $parent[$key] = array(
                        );
                    }
                    $parent = & $parent[$key];
                }
                
                // Handle PHP constants.
                if (preg_match('/^\\w+$/i', $value) && defined($value)) {
                    $value = constant($value);
                }
                
                // Insert actual value.
                if ($last == '') {
                    $last = count($parent);
                }
                $parent[$last] = $value;
            }
        }
        return $info;
    }

    protected function parse_file($filename)
    {
        if (! isset($info[$filename])) {
            if (! file_exists($filename)) {
                $info[$filename] = array(
                );
            } else {
                $data = file_get_contents($filename);
                $info[$filename] = $this->parse_format($data);
            }
        }
        return $info[$filename];
    }
    
    public function render(){
        $this->data = $this->parse_file($this->filename);
        return $this->data;
    }
    
    public function get($key){
        if($this->data == null){
            $this->render();
        }
        return isset($this->data[$key]) ? $this->data[$key] : '';
    }
}
