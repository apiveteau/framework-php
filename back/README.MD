**Framework Project**

***Presentation***
This project want to provide the lighter PHP framework developer-oriented, multi-site, multi-lang, multi-application. Under GNU GPL v2.0

***Team***
Our team is composed by 2 developper : François Gazaube as PHP developper and Anatole Piveteau as Project Maintainer 

*TODO:Add a route /status to get informations about application performances* 

***Strategy*** 

* We need to be lightest as we can, simple request execution time must be <5ms for the core including the templating engine without tiers dependancy

* This project must not depend of database, if we want to get a database connection working, we need to inject it into the kernel.boot event

# Settings


    APPLICATION_ID : Usefull for API app
    
    APPLICATION_NAME : /
    
    APPLICATION_CONTEXT : Define the current context, Production by default, use Develop to get debug access
    
    SITES_DOMAINS : Comma separated list to declare mutli-domain app
    
    DATABASE_USER : User for DB
    
    DATABASE_PASS : Password for DB
    
    DATABASE_HOST : Host of DB
    
    DATABASE_PORT : Port of DB
    
    DATABASE_NAME : Database name
    
    TEMP_FORMAT : Log file format name (to split by date)
    
    LOG_LEVEL : Write all log who had a level under this constant
    
    

# Stack 
    
    
    FRONT RENDERING
    
    1 : Index
        A : ENV_VAR
        
    2 : Loader
        A : Include all interfaces in Core
        B : Include all classes in Core
        C : Include all files from Root
    
    2 : Kernel
        A : Env
        B : Annotation
        C : Routing
        
    3 : Controller
        A : Render
        
    4 : Template
        A : sectionnalize
        C : makeLoop
        B : setVars
        D : [Context=Develop]Debug
    
# Event Stack


    sort by execution
    core/kernel.boot
    core/routing.read
    core/routing.current
        core/routing.500
        core/routing.404
        core/routing.200
    core/template.preProcess
    core/template.preRender
    core/template.postRender
    core/template.postProcess
    
## Norm


    1 : Upper Camel Case for classes
    
    2 : Lower Camel Case for methods
    
    3 : NameSpace :
        Interface : Core\Functionnality
        Class : Core
        
    4 : LOG :
        Format : 
            Key = class
            Message : CLASS|Message of log
            Status : 0 = Normal, 1 = Warning, 2 = Error
        
    5 : Routing :
        .routing Format File : 
            "siteDomain": {
                "route" => "controllerPath",
                ...
            }
        Annotation :
            use the "@route /my/route" flag into your phpdoc controller method
            
    6 : Event :
         use the "@event Namespace/class.event" flag into your phpdoc static method or the Event::add($eventName, &$args)

# Annotation

    Available Annotation :
        @event domain/class.event
        @method PUT/GET/POST/DELETE
        @site site.tld
        @route /path
        @type varchar/integer/text...
        @size int
        @nullable bool
        @foreign Class\Name
# Manual


    0 : Kernel -
        static class
        static function getEnvironment() | Return Env instance
        static function getAnnotation() | Return Annotation instance

    1 : Loader -
        static class
        static function explore($path, $needle, $constraint) | This function include all PHP files
        static $CLASSES | Array of loaded classes path
        
    2 : Env -
        Access from kernel
        public function get($key) | Return the associated value in configuration array or the all array
        
    3 : Files -
        static class
        static function read($path) | Return the content of file given
        static function put($path, $content) | Put at the end of file the $content given
        static function delete($path) | Delete the given file
        static function create($path, $content = null) | Create the file and add content if specified
        static function test($path) | Test if a $path exist, create the missing folder if there are
        
    4 : Annotation
        Acces from kernel
        public function getDocumentation($classname, $method = "") | Get the documentation array of specific class/method
        
    5 : Event
        static class
        static function add($eventName, $classnameAndMethod = null) | Add a listener to an event, create event if no listener gave
        static function exec($eventName, &$args) | Execute all listener associated to given event
        
    6 : Routing
        access from kernel
        public $status | Edit the current status code
        public functon getCurrent() | Return the current route use and the response status code
        
    7 : Template
        static class
        static function boot(&$buffer, &$args) | Make the template dynamic
        static function render($templatePath, $args) | This function render a template defined by path with given args
        static function build($buffer) | This function take a parameter containing the Template content and call other method to search and replace values
        static function sectionalize($buffer) | This function search and replace recursively section in templates
        static function showArray($array) | This function show an array as ul>li HTML recursively
        static function debug(&$buffer) | This function add the marker {debug:varname} to frontend
        static function object_to_array($obj) | This function convert an object to an array recursively
        
    8 : Response
        static function setHeader(string|array) | Set one or many header, value can be  a header string or a property => value array
        static function setStatus(int $code) | Set http response status code
        
    9 : Session
        Create a session with this code, a token file will be generate :
            Session::set($username, $password, $access = []);
        You can close a session with 2 methods, set its timeout to 0 like timelimit, or delete his token file on Cache folders
        With each methods the result will be a logout
        $_SESSION["timeout"] = 0;
        A session is linked to a php session
        
    10 : Database
        Database can be acceed by using the Manager static class, available method are :
            - Manager::getConnection(driver = "mysql")
        Each connection type must implement the Connection Interface who contain these methods 
            /**
             * Return driver name
             * @return string
             */
            public function getName();
            /**
             * @param $reader
             * @return mixed
             */
            public function setModelReader(&$reader);
            /**
             * Return a QueryBuilder instance
             * @param string $table
             * @return mixed
             */
            public function getQueryBuilder($table = "");
            /**
             * Execute a query generate by QueryBuilder
             * @param string $query
             * @return mixed
             */
            public function exec($query = "");
            /**
             * @param $elements
             * @param array $result
             * @return array<\Core\Database\Model\Model>
             */
            public function convert(&$elements, $result = []);
            /**
             * @param string $classname
             * @return string $tablename
             */
            public function getTableName($classname);
        QueryBuilders need to implement QueryBuilder interface who contain these method
            /**
             * @param $fields
             * @return mixed
             */
            public function select($fields);
            /**
             * @param $tablename
             * @return mixed
             */
            public function update($tablename);
            /**
             * @param $tablename
             * @return mixed
             */
            public function delete($tablename);
            /**
             * @param $tablename
             * @return mixed
             */
            public function insert($tablename);
            
        Simple way to protect a controller with session
            
            public function __construct()
            {
                if (!Session::get())
                    $this->redirect($_SERVER["HTTP_HOST"] . "/404" . $_SERVER["REQUEST_URI"]);
            }
        
# Template :

    Template engine works as pseudo-execution code, [if] in template do a if in code, [foreach] too

    # SECTION :
    
    {section:pathFromRoot/section}
    {section:pathFromRoot/section2}
    
    Section can call other section recursively

    # DIPLAY :
        {message}
        {submessage.0}
        {submessage.1}
        {submessage.2}
        {submessage.suite}
        {submessage.suite2.0}
        {submessage.suite2.1}
        {submessage.suite2.2}
        {debug:__args}
    
    {vars} / {vars.subkey} / {vars.subkey.subkey.[...].sunkey} : Display a var
    {debug:var} : Show a full var, special value is __args who show all arguments in template
    
    # IF :
    [if:{coucou} === "Bonjours"]
        {then}
            {coucou} === "Bonjours"
        {:then}
        {else}
            {coucou} !== "Bonjours"
        {:else}
    [if]
    
    Condition format : {var} === "blabla" || {var} === "0"        
        - All dynamic value will be compared as string, so if you use boolean or int do that
            {var} === "false" ...
    
    # LOOP : 
    [foreach:array as element]
            <li>'{key:element}:{element}'</li>
    [foreach]
    
    # FUNC :
    {convert.date:timestamp}
    {convert.html:vars}
    {convert.json:vars}
    {crop.XXX: {vars}}
    
    # ASSETS :
    {assets:pathFromRoot/file.ext} // From Modules directory
    
    
# Test
    Install PHPUNIT in Tests/Phpunit folder
    To load test use a command like ./phpunit Tests/Security/SecurityTest.php
    in git BASH
    
    
# Catch 404

    /**
     * @param $current
     * @event core/kernel.boot
     */
    public static function catchNotFoundException(&$current) {
        $page = (new PageRepository())->getCurrentPage();
        if (Kernel::$routing->status === 404) {
            Response::setStatus(200);
            Kernel::$routing->define($_SERVER["REQUEST_URI"], DefaultController::class . "->notFound", $_SERVER["HTTP_HOST"], []);
        }
    }