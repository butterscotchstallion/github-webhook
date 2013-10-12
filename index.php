<?php
/**
 * Github webhook receiver
 *
 */
require 'db.php';

error_reporting(-1);
$debug = false;

/**
 * The Public IP addresses for these hooks are: 
 * - 204.232.175.64/27
 * - 192.30.252.0/22
 * -- https://github.com/your-username/guacbot/settings/hooks
 *
 */
$ip        = $_SERVER['REMOTE_ADDR'];
$ranges    = array('204.232.175.64/27', '192.30.252.0/22');
$ipInRange = false;

// Check if originating IP address is in the github ranges
foreach ($ranges as $key => $r) {
    $match = cidr_match($ip, $r);
    
    if ($match) {
        $ipInRange = true;
        break;
    }
}

if ($debug || $ipInRange) {
    $payload = isset($_POST['payload']) ? $_POST['payload'] : false;
    
    if ($payload) {
        // Decode payload as an array
        $decoded = json_decode($payload, true);
        $error   = json_last_error();
        
        if (is_array($decoded) && $decoded && $error === JSON_ERROR_NONE) {
            $commits         = isset($decoded['commits']) ? $decoded['commits'] : array();
            $numberOfCommits = count($commits);        
            $connection      = getConnection();
            
            if ($connection instanceof PDO) {
                $query = "INSERT INTO github_push_notifications (payload,
                                                                 number_of_commits,
                                                                 created_at)
                          VALUES (:payload, :numberCommits, NOW())";
                $stmt = $connection->prepare($query);
                $stmt->execute(array(':payload'       => $payload,
                                     ':numberCommits' => $numberOfCommits));
                                     
                $notificationID = $connection->lastInsertId();
                
                if ($notificationID) {
                    echo 'ok';
                }
            }
        } else {
            error_log('Error parsing payload');
        }
    }
    
} else {
    header('HTTP/1.0 404 Not Found');
    die;
}

// http://stackoverflow.com/a/594134/124529
function cidr_match($ip, $range) {
    list ($subnet, $bits) = explode('/', $range);
    $ip = ip2long($ip);
    $subnet = ip2long($subnet);
    $mask = -1 << (32 - $bits);
    $subnet &= $mask; # nb: in case the supplied subnet wasn't correctly aligned
    return ($ip & $mask) == $subnet;
}

if ($debug) {
?>

<form method="post" action="">
    <textarea name="payload" cols="100" rows="30"><?php echo file_get_contents('example-payload.json');?></textarea>
    <br>
    <input type="submit" value="deploy">
</form>

<?php
}
