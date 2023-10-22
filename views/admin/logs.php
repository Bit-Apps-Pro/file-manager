<?php

use BitApps\FM\Config;
use BitApps\FM\Http\Services\LogService;
use BitApps\FM\Plugin;

if (!\defined('ABSPATH')) {
    exit();
}
$currentPage = 1;
$limit       = 10;
if (isset($_GET['log-page'])) {
    $currentPage = \intval($_GET['log-page']);
}
if (isset($_GET['limit'])) {
    $limit = \intval($_GET['limit']);
}
$pageUrl  = Config::get('ADMIN_URL') . 'admin.php?page=file-manager-logs&log-page=';
$logs     = new LogService();
$logsData = $logs->all((($currentPage - 1) * $limit), $limit);
$allLogs  = $logsData['data'];
$count    = $logsData['count'];

$totalPage = ceil($count / $limit);
?>
<?php require_once 'header.php'; ?>
<style>
  .table {
  display: inline-block;
  vertical-align: top;
  max-width: 100%;
  overflow-x: auto;
  white-space: nowrap;
  border-collapse: collapse;
  border-spacing: 0;
 }

.table tbody {
  -webkit-overflow-scrolling: touch;
  background: radial-gradient(left, ellipse, rgba(0,0,0, .2) 0%, rgba(0,0,0, 0) 75%) 0 center,
              radial-gradient(right, ellipse, rgba(0,0,0, .2) 0%, rgba(0,0,0, 0) 75%) 100% center;
  background-size: 10px 100%, 10px 100%;
  background-attachment: scroll, scroll;
  background-repeat: no-repeat;
}

.table td:first-child,
 tbody tr:first-child {
  background-image: linear-gradient(to right, rgba(255,255,255, 1) 50%, rgba(255,255,255, 0) 100%);
  background-repeat: no-repeat;
  background-size: 20px 100%;
}
.table td:last-child,
tbody tr:last-child {
  background-image: linear-gradient(to left, rgba(255,255,255, 1) 50%, rgba(255,255,255, 0) 100%);
  background-repeat: no-repeat;
  background-position: 100% 0;
  background-size: 20px 100%;
}

.table th {
  font-size: 11px;
  text-align: left;
  text-transform: uppercase;
  background: #f2f0e6;
}

.table th,
.table td {
  padding: 6px 12px;
  border: 1px solid #d9d7ce;
}
.pagination {
  display: flex;
  padding: 10px;
  justify-content: center;
}
.pagination a {
  text-decoration: none;
  padding: 0 10px;
}
</style>
<div class='fm-container'>
  <div class='col-main'>
    <table class="table">
        <thead>
            <tr>
            <th>User</th>
            <th>Command</th>
            <th>Details</th>
            <th>Created</th>
            </tr>
        </thead>
        <tbody>
          <?php foreach ($allLogs as $log) { ?>
            <tr>
            <td><?php echo esc_html(Plugin::instance()->permissions()->getUserDisplayName($log->user_id));?></td>
            <td><?php echo esc_html($log->command);?></td>
            <td>
             Driver: <?php
              echo isset($log->details->driver) ? esc_html(str_replace('elFinderVolume', '', $log->details->driver)) : '';
             ?>
             <br/>
             <?php if (isset($log->details->files)) { ?>
              Files:<br/>
              <?php foreach ($log->details->files as $index => $file) { ?>
                &check; <?php echo esc_html($file->path);?><br/>
             <?php }?>
             <?php }?>
            </td>
            <td><?php echo esc_html($log->created_at);?></td>
            </tr>
          <?php }?>
        </tbody>
    </table>
    <div class="pagination">
    <?php if ($currentPage > 1) {?>
      <a href="<?php echo esc_attr($pageUrl . ($currentPage - 1));?>">Previous</a>
      <?php }?>
      <?php if ($currentPage < $totalPage) {?>
        <a href="<?php echo esc_attr($pageUrl . ($currentPage + 1));?>">Next</a>
<?php }?>
    </div>
  </div>
    <?php require_once 'sidebar.php'; ?>
</div>
<?php require_once 'footer.php'; ?>
