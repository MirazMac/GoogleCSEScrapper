<?php

use MirazMac\GoogleCSE\Scrapper;

require '../vendor/autoload.php';

$cse = new Scrapper('partner-pub-9134522736300956:4140494421', []);

$q = isset($_GET['q']) ? trim($_GET['q']) : false;
$safeQ = htmlspecialchars($q);
$start = isset($_GET['start']) ? (int) $_GET['start'] : 0;
$spell = isset($_GET['spell']) ? (int) $_GET['spell'] : null;
$nfpr = isset($_GET['nfpr']) ? (int) $_GET['nfpr'] : null;

$params = [];

if ($spell) {
    $params['spell'] = $spell;
} elseif ($nfpr) {
    $params['nfpr'] = $nfpr;
}

unset($_GET['spell'], $_GET['q'], $_GET['nfpr']);

$get = @$_GET;
$params = array_merge($params, $get);
//r($params);
?>

<div class="container">
    <div class="fix">
        <form method="get" action="?">
            <input type="search" name="q" placeholder="Enter query to search.." value="<?=htmlspecialchars($q)?>">
            <button type="submit">Search</button>
        </form>
    </div>
    <?php if (empty($q)) :?>
        <p>Please input a query to search!</p>
    <?php else :?>
        <?php
        // Search the web with the query
        $results = $cse->searchImage($q, $start, 20, $params);
        ?>
        <?php if ($entries = $results->getAll()) :?>
            <div class="block">
                Found <strong><?=number_format($results->getEstimatedResultCount())?></strong> results for <strong><?=$safeQ?></strong> in
                <strong><?=$results->getSearchResultTime()?></strong> seconds
            </div>

            <?php if ($results->isSpellingMistake()) :?>
                <div class="block">
                    <?php if ($results->hasDidYouMean()) :?>
                        Did you mean <a href="?q=<?=$results->getRawCorrectedQuery()?>"><?=$results->getCorrectedQuery()?></a>
                    <?php endif;?>
                    <?php if ($results->hasCorrectedResults()) :?>
                        Showing results for <a href="?q=<?=$results->getRawCorrectedQuery();?>&spell=1"><?=$results->getCorrectedQuery()?></a>.<br/>
                        Search for <a href="?q=<?=$results->getRawOriginalQuery();?>&nfpr=1"><?=$results->getOriginalQuery()?></a>  instead.
                    <?php endif;?>
                </div>
            <?php endif;?>

            <?php foreach ($entries as $res) :?>
                <div class="block float">
                    <a href="<?=$res->getRawURL()?>" class="serp">
                        <img src="<?=$res->getThumbnailURL()?>" alt="<?=$res->getTitle()?>"></a>
                    <span class="url"><?=$res->getVisibleURL()?></span>
                    <p class="excerpt"><?=$res->getContent()?></p>
                </div>
            <?php endforeach;?>
            <div class="block fix">
                <?php foreach ($results->getPages() as $page) :?>
                    <a href="?q=<?=$safeQ?>&start=<?=$page['start']?>" class="page"><?=$page['label']?></a>
                <?php endforeach;?>
            </div>
        <?php else :?>
            <p>No results found for your query <?=$safeQ?></p>
        <?php endif;?>
    <?php endif;?>
</div>
<style type="text/css">
.container { max-width:720px;margin:auto;font-family:sans-serif;}
.fix { overflow:hidden;}
input {display:inline-block;width:85%;padding: 10px 12px;border-radius: 3px;border: 1px solid #e0e0ec;float:left}
button {border:1px solid #3b9;background:#3b9;color:#fff;padding: 10px 12px;width:15%;display:inline-block;}
.block {padding:12px 10px;}
.float {
    float: left;
    width: 40%;
    text-align: center;
}
.fix {clear:both;overflow: hidden;}
.serp {color:#0000FF;text-decoration:none;font-size:16px}
.url {display:block;color:#008000;padding:2px 0;font-size:14px}
.excerpt {margin:2px 0;color:#787878;font-size:13px}
.page {display: inline-block;padding: 4px 10px;}
</style>
