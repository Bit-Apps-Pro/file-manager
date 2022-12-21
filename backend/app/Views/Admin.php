<?php

namespace BitApps\FM\Views;

use BitApps\FM\Config;
use BitApps\FM\Core\Database\Connection;
use BitApps\FM\Model\Flow;
use BitApps\FM\Model\FlowLog;

final class Admin
{
  public function home()
  {
    // $flow = FlowModel::join('btcfi_log', 'id')->orOn('test')->where('details2', 'LIKE', '%sd%')->desc()->get();
    // $flow = FlowModel::join('btcfi_log as l', 'id')->where('type2', 234234)->delete()->prepare(null);
    // $flow = FlowModel::destroy([234234,234234])->prepare(null);
    Connection::enableQuery();
    $flow = Flow::with('logs', function ($q) {
      $q->where('id', '>', 1);
    } )->findOne(['id' => 1]);
    echo '<pre>';
    var_dump($flow, FlowLog::count());
   /*  if ($flow) {
      var_dump($flow->getAttributes());
    } */
    /* $flow->type2 = 234234;
        $flow->test = ['fsdfksdf' => 22];
        $flow->save(); */
    echo <<<MARKUP
<noscript>You need to enable JavaScript to run this app.</noscript>
<div id="bit-flow-root">
  <div
    style="display: flex;flex-direction: column;justify-content: center;align-items: center;height: 90vh;font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <img alt="bitform-logo" class="bit-logo" width="70" src="{Config::get('ASSET_URI')}">/img/logo.svg">
    <h1>Welcome to Bit Flow.</h1>
    <p></p>
  </div>
</div>
MARKUP;
  }
}
