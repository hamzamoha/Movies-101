<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus</title>
    <link rel="stylesheet" href="/new/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        body {
            background: url("https://images7.alphacoders.com/720/720818.jpg") no-repeat center 0% / cover fixed;
        }
    </style>
</head>

<body class="bg-slate-900 text-white">
    <div id="main" class="min-h-screen bg-slate-900/90">
        <div class="mx-auto px-5 max-w-screen-xl w-full">
            <div class="py-5 px-1 text-center">
                <h1 class="text-8xl text-amber-500 px-2 font-semibold">
                    <a href="/new" class="block">Nexus</a>
                </h1>
                <p class="text-lg text-neutral-300 px-2">Watch and Download Movies - Torrent</p>
            </div>
            <div class="py-5">
                <form action="?" method="get" autocomplete="off" class="relative">
                    <input type="text" required name="q" placeholder="Search a Movie..." class="block w-full heading-10 px-1.5 py-2.5 rounded-lg outline-none bg-slate-800 focus:shadow-[0_0_25px_10px] shadow-[0_0_15px_5px] shadow-amber-500/30 focus:shadow-amber-500/50 border border-amber-600 focus:border-amber-500 duration-300">
                </form>
            </div>
            <?php
            if (isset($_GET['imdb'])) {
                $movie = json_decode(file_get_contents("https://yts.mx/api/v2/movie_details.json?imdb_id=" . urlencode($_GET['imdb'])))->data->movie;
                if ($movie->id == 0) { ?>
                    <div class="text-center py-3 text-3xl font-light">No Movies Found</div>
                <?php } else { ?>
                    <div class="py-5">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 py-5">
                            <div>
                                <div class="bg-teal-400 rounded p-1 max-w-full w-72 mx-auto">
                                    <img src="<?= $movie->large_cover_image ?>" alt="<?= $movie->title_long ?>" title="<?= $movie->title_long ?>" class="block">
                                </div>
                            </div>
                            <div class="col-span-3">
                                <h1 class="py-2 text-4xl font-medium"><?= $movie->title_long ?></h1>
                                <div class="flex flex-wrap gap-5 py-2 text-lg">
                                    <div>
                                        <svg viewBox="0 0 24 24" class="stroke-white fill-white w-5 inline">
                                            <path d="M20,3a1,1,0,0,0,0-2H4A1,1,0,0,0,4,3H5.049c.146,1.836.743,5.75,3.194,8-2.585,2.511-3.111,7.734-3.216,10H4a1,1,0,0,0,0,2H20a1,1,0,0,0,0-2H18.973c-.105-2.264-.631-7.487-3.216-10,2.451-2.252,3.048-6.166,3.194-8Zm-6.42,7.126a1,1,0,0,0,.035,1.767c2.437,1.228,3.2,6.311,3.355,9.107H7.03c.151-2.8.918-7.879,3.355-9.107a1,1,0,0,0,.035-1.767C7.881,8.717,7.227,4.844,7.058,3h9.884C16.773,4.844,16.119,8.717,13.58,10.126ZM12,13s3,2.4,3,3.6V20H9V16.6C9,15.4,12,13,12,13Z"></path>
                                        </svg>
                                        <?= (intdiv($movie->runtime, 60) > 0 ? intdiv($movie->runtime, 60) . "h " : "") . ($movie->runtime % 60) . "min" ?>
                                    </div>
                                    <div>
                                        <span class="font-semibold"><?= $movie->rating ?></span><span class="text-base">/10</span>
                                    </div>
                                    <div>
                                        <svg viewBox="0 0 24 24" class="fill-amber-500 inline w-5" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M2 9.1371C2 14 6.01943 16.5914 8.96173 18.9109C10 19.7294 11 20.5 12 20.5C13 20.5 14 19.7294 15.0383 18.9109C17.9806 16.5914 22 14 22 9.1371C22 4.27416 16.4998 0.825464 12 5.50063C7.50016 0.825464 2 4.27416 2 9.1371Z"></path>
                                        </svg>
                                        <?= $movie->like_count ?>
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-2 py-2 text-md">
                                    <?php foreach ($movie->genres as $genre) {
                                    ?>
                                        <div class="p-1 border"><?= $genre ?></div>
                                    <?php } ?>
                                </div>
                                <div class="text-sm py-2 flex flex-wrap gap-3 items-center">
                                    <span>Download:</span>
                                    <?php
                                    $trackers = [
                                        "udp://glotorrents.pw:6969/announce",
                                        "udp://tracker.opentrackr.org:1337/announce",
                                        "udp://torrent.gresille.org:80/announce",
                                        "udp://tracker.openbittorrent.com:80",
                                        "udp://tracker.coppersurfer.tk:6969",
                                        "udp://tracker.leechers-paradise.org:6969",
                                        "udp://p4p.arenabg.ch:1337",
                                        "udp://tracker.internetwarriors.net:1337"
                                    ];
                                    $servers = [
                                        "https://vidsrc.to/embed/movie/$movie->imdb_code",
                                        "https://database.gdriveplayer.us/player.php?imdb=$movie->imdb_code",
                                        "https://multiembed.mov/directstream.php?video_id=$movie->imdb_code",
                                        "https://frembed.pro/api/film.php?id=$movie->imdb_code",
                                        //"https://justbinge.lol/embed/movie/$movie->imdb_code",
                                        "https://vidsrc.xyz/embed/movie/$movie->imdb_code",
                                        "https://vidsrc.pro/embed/movie/$movie->imdb_code"
                                    ];
                                    foreach ($movie->torrents as $torrent) { ?>
                                        <a class="block py-1 px-1.5 bg-slate-600 duration-300 hover:bg-slate-500 rounded border" href="<?= "magnet:?xt=urn:btih:" . $torrent->hash . "&dn=" . urlencode($movie->title_long) . "&tr=" . implode("&", $trackers) ?>"><?= $torrent->quality . "." . $torrent->type . $torrent->video_codec ?></a>
                                    <?php } ?>
                                </div>
                                <div class="text-neutral-300 indent-5 py-5 text-lg">
                                    <p><?= $movie->description_full ?></p>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h2 class="text-5xl font-semibold my-3 text-center">Watch</h2>
                            <div class="p-1 bg-amber-500 max-w-full w-fit flex flex-nowrap text-center rounded-md mx-auto my-3 gap-2">
                                <?php foreach ($servers as $i => $server) { ?>
                                    <div class="wordwrap-nowrap">
                                        <input type="radio" name="server" class="peer hidden" id="server_<?= $i + 1 ?>" value="<?= addslashes($server) ?>">
                                        <label for="server_<?= $i + 1 ?>" class="p-1 block rounded peer-checked:bg-slate-900 peer-checked:text-white text-slate-900 cursor-pointer">Server <?= $i + 1 ?></label>
                                    </div>
                                <?php } ?>
                            </div>
                            <iframe id="player" src="" width="100%" height="600" class="outline-none bg-slate-500" allowfullscreen="true" loading="lazy" referrerpolicy="no-referrer"></iframe>
                        </div>
                    </div>
                <?php }
            } else {
                $last_added = json_decode(file_get_contents("https://yts.mx/api/v2/list_movies.json?limit=4"))->data->movies;
                $most_liked = json_decode(file_get_contents("https://yts.mx/api/v2/list_movies.json?sort_by=like_count&limit=4"))->data->movies;
                $most_downloaded = json_decode(file_get_contents("https://yts.mx/api/v2/list_movies.json?sort_by=download_count&limit=4"))->data->movies;
                if (isset($_GET['q'])) {
                    $search_result = json_decode(file_get_contents("https://yts.mx/api/v2/list_movies.json?limit=12&query_term=" . urlencode($_GET['q'])))->data->movies;
                ?>
                    <div class="py-5">
                        <h1 class="text-6xl text-center py-5 px-1 font-medium">Search Result</h1>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 py-5">
                            <?php
                            foreach ($search_result as $movie) { ?>
                                <a href="?imdb=<?= $movie->imdb_code ?>" class="block hover:scale-[1.05] duration-300">
                                    <div class="bg-teal-400 rounded p-1">
                                        <img src="<?= $movie->large_cover_image ?>" alt="<?= $movie->title_long ?>" title="<?= $movie->title_long ?>" class="block">
                                    </div>
                                    <h2 class="py-2 text-xl font-medium text-center"><?= $movie->title_long ?></h2>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
                <div class="py-5">
                    <h1 class="text-6xl text-center py-5 px-1 font-medium">Last Added</h1>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-8 py-5">
                        <?php
                        foreach ($last_added as $movie) { ?>
                            <a href="?imdb=<?= $movie->imdb_code ?>" class="block hover:scale-[1.05] duration-300">
                                <div class="bg-teal-400 rounded p-1">
                                    <img src="<?= $movie->large_cover_image ?>" alt="<?= $movie->title_long ?>" title="<?= $movie->title_long ?>" class="block">
                                </div>
                                <h2 class="py-2 text-xl font-medium text-center"><?= $movie->title_long ?></h2>
                            </a>
                        <?php } ?>
                    </div>
                </div>
                <div class="py-5">
                    <h1 class="text-6xl text-center py-5 px-1 font-medium">Most Liked</h1>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-8 py-5">
                        <?php
                        foreach ($most_liked as $movie) { ?>
                            <a href="?imdb=<?= $movie->imdb_code ?>" class="block hover:scale-[1.05] duration-300">
                                <div class="bg-teal-400 rounded p-1">
                                    <img src="<?= $movie->large_cover_image ?>" alt="<?= $movie->title_long ?>" title="<?= $movie->title_long ?>" class="block">
                                </div>
                                <h2 class="py-2 text-xl font-medium text-center"><?= $movie->title_long ?></h2>
                            </a>
                        <?php } ?>
                    </div>
                </div>
                <div class="py-5">
                    <h1 class="text-6xl text-center py-5 px-1 font-medium">Most Downloaded</h1>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-8 py-5">
                        <?php
                        foreach ($most_downloaded as $movie) { ?>
                            <a href="?imdb=<?= $movie->imdb_code ?>" class="block hover:scale-[1.05] duration-300">
                                <div class="bg-teal-400 rounded p-1">
                                    <img src="<?= $movie->large_cover_image ?>" alt="<?= $movie->title_long ?>" title="<?= $movie->title_long ?>" class="block">
                                </div>
                                <h2 class="py-2 text-xl font-medium text-center"><?= $movie->title_long ?></h2>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>


        </div>
    </div>
    <script>
        var radios = document.querySelectorAll('input[type=radio][name="server"]');
        radios.forEach(radio => {
            radio.addEventListener("change", () => {
                let server = document.querySelector('input[type=radio][name="server"]:checked')
                if (server) {
                    document.querySelector("iframe#player").setAttribute("src", server.value)
                }
            })
        });
    </script>
</body>

</html>