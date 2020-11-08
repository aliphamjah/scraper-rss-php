<?php

function rssSource($search)
{
    $rssSource = [
        'Detik' => 'http://rss.detik.com',
        'Tempo' => 'http://rss.tempo.co/nasional',
        'Media Indonesia' => 'https://mediaindonesia.com/feed',
        'CNN Idonesia' => 'https://www.suara.com/rss/news',
        'Kumparan' => 'https://lapi.kumparan.com/v2.0/rss/',
    ];

    $temp = [];
    foreach ($rssSource as $key => $value) {
        $xml = simplexml_load_file($value);
        $row = 1;
        $limit = 4;
        if (is_array($xml->channel->item) || is_object($xml->channel->item)) {
            foreach ($xml->channel->item as $data) {
                if ($row <= $limit) {
                    if (!empty($search)) {
                        if (stristr($data->title, $search) || stristr($data->description, $search)) {
                            $temp[] = array(
                                'source' => $key,
                                'title' => $data->title,
                                'description' => preg_replace("/<img[^>]+\>/i", "", $data->description),
                                'thumbnail' => $data->enclosure ? $data->enclosure['url'] : $data->image,
                                'date' => $data->pubDate,
                                'link' => $data->link
                            );
                        }
                    } else {
                        $temp[] = array(
                            'source' => $key,
                            'title' => $data->title,
                            'description' => preg_replace("/<img[^>]+\>/i", "", $data->description),
                            'thumbnail' => $data->enclosure ? $data->enclosure['url'] : $data->image,
                            'date' => $data->pubDate,
                            'link' => $data->link
                        );
                    }
                }
                $row++;
            }
        }
    }
    return $temp;
}


$search_param = isset($_GET['cari']) ? $_GET['cari']:'';

$raw_rss = rssSource($search_param);

// print_r($raw_rss);
// exit();
echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Berita Terbaru</title>
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css' integrity='sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2' crossorigin='anonymous'>
</head>
<body>
    <div class='w-100 row justify-content-center'>
        <div class='col-8 mb-5'>
            <h2 class='my-4 text-info text-center'>Berita Terbaru</h2>
            <div class='mb-3'>
                <form method='GET'>
                    <div class='input-group mb-3'>
                        <input type='text' class='form-control' placeholder='Pencarian' name='cari' aria-describedby='search-button' value='".$search_param."'>
                        <div class='input-group-append'>
                            <button class='btn btn-outline-secondary' type='submit' id='search-button'>Cari</button>
                        </div>
                    </div>
                </form>
            </div>";
            foreach ($raw_rss as $item) {
                // print_r($item);
                $thumbnail = $item['thumbnail'] != '' ? $item['thumbnail'] : 'https://dummyimage.com/600x400/000/e6e6e6.jpg';
                echo "<div class='card'>
                    <div class='card-body'>
                        <div class='media'>
                            <img src='".$item['thumbnail']."' class='mr-3 w-25' alt='...'>
                            <div class='media-body'>
                                <h5 class='mt-0 text-secondary'><span class='badge badge-secondary'>".$item['source']."</span> ".$item['date']."</h5>
                                <h5 class='mt-0'><a href='".$item['link']."' target='_blank'>".$item['title']."</a></h5>
                                ".$item['description']."
                            </div>
                        </div>
                    </div>
                </div>";
            }
        echo "</div>
        </div>
    </body>
</html>";