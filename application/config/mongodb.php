<?php 
/*
 * @Copy Right Borama Consulting
 * 
 * @kagnara
 * 
 * @7/30/2014 1:48:45 PM
 * 
 * @kagnara , 4/1/2015 4:23:45 PM
 * 
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Generally will be localhost if you're querying from the machine that Mongo is installed on
$config['mongo_host'] = "188.166.183.25";  // ip server on cloud

// Generally will be 27017 unless you've configured Mongo otherwise
$config['mongo_port'] = 27017;

// The database you want to work from (required)
// $config['mongo_db'] = "socialBus_db_dev";
$config['mongo_db'] = "obs_v1";

// Leave blank if Mongo is not running in auth mode
$config['mongo_user'] = "";
$config['mongo_pass'] = "";

// Persistant connections
$config['mongo_persist'] = TRUE;
$config['mongo_persist_key'] = 'ci_mongo_persist';

// add more mongo db replicaset
// $config['mongo_replica_set']  = 'mongors';
// $config['readPreference']   = 'primaryPreferred';

// Get results as an object instead of an array
$config['mongo_return'] = 'array'; // Set to object

// When you run an insert/update/delete how sure do you want to be that the database has received the query?
// safe = the database has receieved and executed the query
// fysnc = as above + the change has been committed to harddisk <- NOTE: will introduce a performance penalty
$config['mongo_query_safety'] = 'w';

// Supress connection error password display
$config['mongo_supress_connect_error'] = TRUE;

// If you are having problems connecting try changing this to TRUE
$config['host_db_flag'] = FALSE;

@include_once "mongodb-local.php";  // overide ip to use local server instead