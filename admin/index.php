<?php
include 'check_admin.php';
if (!$isadmin) header("Location: ../login.php");
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $imdb_code = $_POST['imdb_code'];

    //Connection info
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "project";

    //Connect to db
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        $array = array("message" => "DB Error", "details" => "Error while connecting to db.");
        print_r(json_encode($array));
        exit();
    }

    //query
    if (isset($_POST['url'])) {
        $url = $_POST['url'];
        $query = "UPDATE `movies` SET `movie_url`='$url' WHERE `imdb_code`='$imdb_code'";
        $delete_poster = false;
    } else {
        $query = "DELETE FROM `movies` WHERE `imdb_code`='$imdb_code'";
        $delete_poster = true;
    }
    if ($conn->query($query) === TRUE) {
        $array = array("message" => "success", "details" => "Done!");
        if ($delete_poster) {
            $mask = "../imgs/movies posters/$imdb_code.*";
            array_map('unlink', glob($mask));
        }
    } else $array = array("message" => "Datbase Error", "details" => "Url was not updated: query or mysql error");
    //exit
    $conn->close();
    print_r(json_encode($array));
    exit();
}
?>
<html>

<head>
    <link rel="stylesheet" href="../fonts.css" />
    <link rel="stylesheet" href="../css/loading.css" />
    <link href="/fonts/fontawesome-5.14.0/css/all.css" rel="stylesheet">
    <script src='https://code.jquery.com/jquery-3.5.1.min.js'></script>
    <script src='https://rawgit.com/jackmoore/autosize/master/dist/autosize.min.js'></script>
    <title>Dashboard</title>
    <style>
        * {
            padding: 0;
            margin: 0;
            border: 0;
            text-decoration: none;
            list-style: none;
            font-family: 'TitilliumWeb';
            outline: none;
        }

        #wrapper {
            width: 1200px;
            margin: 0 auto;
            background: #f3f3f3;
            max-width: 100%;
        }

        header .top-header h1 {
            text-align: center;
            font-size: 50px;
            line-height: 150px;
            background: #131313;
            color: #f7be10;
        }

        header nav ul {
            background: #f3f3f3;
        }

        header nav ul:after {
            content: '';
            display: block;
            clear: both;
        }

        header nav ul li {
            float: left;
            position: relative;
        }

        header nav ul li a {
            display: block;
            line-height: 50px;
            font-size: 20px;
            padding: 15px 0;
            text-align: center;
            width: 50px;
            color: #131313;
            transition: all .4s;
        }

        header nav ul li a:hover {
            background: #131313;
            color: #f7be10;
        }

        header nav ul li .info {
            position: absolute;
            z-index: 2;
            background: rgba(243, 243, 243, 0.95);
            white-space: nowrap;
            padding: 0px 15px;
            left: 50%;
            top: calc(100% + 10px);
            transform: translateX(-50%);
            line-height: 36px;
            font-size: 14px;
            box-shadow: 0 0 3px -1px;
            border-radius: 6px;
            color: #131313;
        }

        /* grid */
        .grid {
            display: none;
            position: relative;
        }

        .grid .movie-card {
            height: 150px;
            margin: 5px 0;
            overflow: hidden;
            box-sizing: border-box;
        }

        .grid .movie-card .poster {
            height: 100%;
            width: 150px;
            overflow: hidden;
            float: left;
            background: rgba(0, 0, 0, 0.05);
        }

        .grid .movie-card .poster img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .grid .movie-details {
            width: calc(100% - 150px);
            display: inline-block;
            height: 100%;
            overflow: hidden;
        }

        .grid .movie-details h4 {
            line-height: 50px;
            font-size: 20px;
            padding: 0 10px;
            box-sizing: border-box;
        }

        .grid .movie-details .section {
            line-height: 50px;
            height: 25px;
            padding: 12.5px 10px;
        }

        .grid .movie-details .year {
            text-decoration: underline;
            display: block;
            float: left;
            line-height: 25px;
            color: #888;
        }

        .grid .movie-details .year:hover {
            text-decoration: underline;
        }

        .grid .movie-details .genre a {
            color: #f3f3f3;
            box-shadow: 0 0 0 1px #131313;
            background: #131313;
            font-weight: normal;
            padding: 0 5px;
            border-radius: 10px;
            height: 25px;
            display: block;
            float: left;
            line-height: 25px;
            margin: 0 10px;
            transition: all .4s;
        }

        .grid .movie-details .genre {
            height: 25px;
            display: inline-block;
        }

        .grid .movie-details .genre a:hover {
            color: #f7be10;
        }

        .grid .movie-details .buttons {
            height: 50px;
            line-height: 50px;
            padding: 0 10px;
        }

        .grid .movie-details .buttons button {
            background: #f7be10;
            line-height: 30px;
            padding: 0 15px;
            border-radius: 15px;
            color: #131313;
            cursor: pointer;
            float: left;
            margin: 10px 5px 10px 0;
        }

        .grid .movie-details .buttons button[name=update_url] {
            background: #57e27b;
        }

        .grid .movie-details .buttons button[name=update_url]:before {
            content: '\f044  ';
            font-family: 'Font Awesome 5 Free';
        }

        .grid .movie-details .buttons button[name=delete] {
            background: #ff4949;
        }

        .grid .movie-details .buttons button[name=delete]:before {
            content: '\f1f8  ';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
        }

        main .top-main div {
            text-align: center;
            height: 70px;
        }

        main .top-main h2 {
            display: inline-block;
            background: #dcdcdc;
            box-shadow: inset 0 0 0 2px #c5c5c5;
            line-height: 50px;
            margin: 10px 0;
            padding: 0 75px;
            color: #131313;
            border-radius: 25px;
            height: 50px;
        }

        .grid #float_search {
            height: 50px;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .grid #float_search i {
            position: absolute;
            width: 50px;
            text-align: center;
            line-height: 50px;
            border-radius: 50%;
            background: #f7be10;
            font-size: 20px;
            color: #fff;
            right: 0;
            top: 0;
        }

        #float_search input[type="text"] {
            font-size: 20px;
            line-height: 48px;
            text-indent: 20px;
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 25px;
        }

        /* add movie */
        #add-movie {
            position: relative;
        }

        form[name='movie_search'] {
            overflow: hidden;
            position: relative;
        }

        form[name='movie_search'] input[name='search_query'] {
            width: 100%;
            line-height: 50px;
            text-indent: 15px;
            font-size: 15px;
            background: #131313;
            color: #fff;
            border-radius: 25px;
        }

        form[name='movie_search'] input[name="search_active"] {
            position: absolute;
            right: 0;
            top: 0;
            height: 50px;
            width: 50px;
            border-radius: 25px;
            cursor: pointer;
        }

        form[name='movie_search'] .options {
            text-align: center;
            margin: 10px 0;
        }

        form[name='movie_search'] .options input[type=radio] {
            background-color: #f7be10;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            width: 15px;
            height: 15px;
            position: relative;
            border-radius: 50%;
        }

        form[name='movie_search'] .options input[type=radio]:checked:after {
            background: #222;
            content: '';
            position: absolute;
            top: 3px;
            left: 3px;
            width: 9px;
            height: 9px;
            border-radius: 50%;
            cursor: pointer;
        }

        form[name='movie_search'] .options label {
            font-weight: normal;
        }

        form[name='movie_search'] .options strong {
            margin: 0 3px;
        }

        #search_results {
            margin: 10px;
            padding: 10px 0;
        }

        #search_results:after {
            clear: both;
            content: '';
            display: block;
        }

        #search_results .movie-card {
            box-sizing: border-box;
            position: relative;
            border: solid #dcad1e 5px;
            border-radius: 5px;
        }

        #search_results .movie-card-outer {
            width: 20%;
            height: 300px;
            float: left;
            box-sizing: border-box;
            padding: 5px;
            margin: 5px 0;
        }

        #search_results .movie-card img.poster {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        #search_results .movie-card h3.title {
            position: absolute;
            bottom: 0;
            width: 100%;
            text-align: center;
            line-height: 30px;
            max-height: 60px;
            overflow: hidden;
            background: rgba(0, 0, 0, 0.65);
            color: #f7be10;
            font-size: 20px;
            font-family: 'TitilliumWeb';
            font-weight: 600;
            font-style: normal;
            box-sizing: border-box;
            padding: 0 5px;
        }

        #search_results .movie-card .year {
            position: absolute;
            right: 0;
            top: 0;
            z-index: 1;
            background: #dcad1e;
            color: #4e4e4e;
            padding: 5px 10px;
        }

        #search_results .movie-card .add_movie {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            background: rgba(0, 0, 0, 0.4);
            text-align: center;
            transition: all .4s;
            display: none;
        }

        #search_results .movie-card .add_movie button {
            height: 38px;
            padding: 0 25px;
            margin: 126px 0;
            background: #57e27b;
            font-weight: 600;
            color: #131313;
            border-radius: 19px;
            cursor: pointer;
        }

        #load_more {
            height: 50px;
            text-align: center;
            line-height: 50px;
            display: none;
        }

        #load_more input {
            height: 30px;
            line-height: 30px;
            margin: 10px 0;
            padding: 0 30px;
            background: #dcdcdc;
            border-radius: 15px;
            font-size: 15px;
            font-weight: 600;
            color: #131313;
            cursor: pointer;
        }

        .clear_results {
            right: 6px;
            position: absolute;
            display: none;
            top: 90px;
            z-index: 2;
        }

        .clear_results button {
            width: 34px;
            height: 34px;
            font-size: 34px;
            cursor: pointer;
            color: #ff4949;
            border-radius: 50%;
        }

        .clear_results span {
            white-space: nowrap;
            display: none;
            position: absolute;
            top: calc(100% + 10px);
            left: 50%;
            transform: translateX(-50%);
            padding: 0 15px;
            line-height: 44px;
            background: #fffd;
            font-size: 14px;
            border-radius: 5px;
            box-shadow: 0 0 0 1px #c5c5c5;
        }

        .clear_results:hover span {
            display: block;
        }

        .no_result {
            text-align: center;
            line-height: 80px;
        }

        .no_result h4 {
            font-size: 22px;
        }

        /* movie form */
        form[name='movie_form'] {
            display: none;
        }

        form[name='movie_form']:after {
            content: '';
            clear: both;
            display: block;
        }

        form[name='movie_form'] .poster {
            float: left;
            width: 200px;
            height: 300px;
            overflow: hidden;
            padding: 5px;
            box-sizing: border-box;
            background: #f7be10;
            border-radius: 8px;
            margin: 10px;
        }

        form[name='movie_form'] .poster img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        form[name='movie_form'] table {
            float: right;
            width: calc(100% - 220px);
            margin: 10px 0;
            line-height: 40px;
            box-sizing: border-box;
            padding: 5px;
            border-collapse: collapse;
            display: block;
            background: #f7be10;
            border-radius: 8px;
            color: #fff;
        }

        form[name='movie_form'] table tbody {
            width: 100%;
            display: block;
            background: #131313;
            box-sizing: border-box;
            padding: 5px;
        }

        form[name='movie_form'] table tr {
            width: 100%;
            display: block;
        }

        form[name='movie_form'] table tr:after {
            content: '';
            display: block;
            clear: both;
        }

        form[name='movie_form'] table td {
            width: 50%;
            display: block;
            float: left;
        }

        form[name='movie_form'] table label {
            display: block;
            text-align: right;
            padding: 0 10px;
        }

        form[name='movie_form'] table input,
        form[name='movie_form'] table textarea,
        form[name='movie_form'] table select {
            height: 34px;
            line-height: 34px;
            width: 100%;
            box-shadow: 0 0 0px 0 #eee;
            margin: 3px 0;
            box-sizing: border-box;
            padding: 0 10px;
            border-radius: 8px;
            background: #fbfbfb;
        }

        #search_results .success,
        #search_results .failed {
            text-align: center;
            height: 200px;
            line-height: 200px;
            font-size: 25px;
        }

        #search_results .failed {
            color: #d55;
        }

        #search_results .success {
            color: #5d5;
        }

        #update_url,
        #delete_movie {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }

        #update_url form,
        #delete_movie form {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #fff;
            padding: 0 20px;
            width: 500px;
            display: none;
        }

        #update_url h4,
        #delete_movie h4 {
            line-height: 60px;
        }

        #update_url input[name='url'] {
            width: 100%;
            height: 50px;
            margin: 0 auto;
            box-shadow: 0 0 0 1px #bbb;
            border-radius: 5px;
            text-indent: 10px;
        }

        #update_url input[type="submit"] {
            display: block;
            width: 100px;
            height: 36px;
            margin: 20px auto;
            border-radius: 3px;
            cursor: pointer;
            background: #f7be10;
        }

        #update_url i {
            position: absolute;
            top: 13px;
            right: 20px;
            width: 30px;
            line-height: 30px;
            text-align: center;
            cursor: pointer;
            background: #f7be10;
        }

        #update_url p,
        #update_url strong,
        #delete_movie p {
            display: none;
            width: 400px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            background: #fff;
            line-height: 60px;
            border-radius: 3px;
            font-size: 20px;

        }

        #delete_movie input {
            width: calc(50% - 4px);
            margin: 10px 2px;
            line-height: 40px;
            float: left;
        }

        #settings {
            display: none;
            position: relative;
        }

        form#settings_form>* {
            float: left;
            display: block;
            width: 50%;
            overflow: hidden;
            box-sizing: border-box;
            line-height: 30px;
            margin-bottom: 5px;
        }

        form#settings_form:after {
            content: '';
            display: block;
            clear: both;
        }

        form#settings_form>label {
            text-align: right;
            padding-right: 10px;
        }

        form#settings_form>input {
            padding: 0 8px;
            border-radius: 3px;
            box-shadow: 0 0 0 1px #ddd;
        }

        form#settings_form>input[type='button'] {
            cursor: pointer;
        }

        form#settings_form {
            width: 600px;
            margin: 0 auto;
            max-width: 100%;
            padding: 15px 0;
        }

        #password_form {
            display: none;
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background: rgba(0, 0, 0, 0.5);
        }

        #password_form form {
            display: none;
            position: absolute;
            width: 500px;
            top: 50%;
            left: 50%;
            background: rgba(255, 255, 255, 1);
            transform: translate(-50%, -50%);
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #c5c5c5;
        }

        #password_form form:after {
            content: '';
            clear: both;
            display: block;
        }

        #password_form form>* {
            float: left;
            line-height: 30px;
            width: calc(50% - 10px);
            box-sizing: border-box;
            border-radius: 3px;
            margin: 5px;
        }

        #password_form form input[type="reset"],
        #password_form form input[type="submit"] {
            cursor: pointer;
        }

        #password_form form input[type="password"] {
            box-shadow: 0 0 0 1px #eee;
            text-indent: 8px;
        }
    </style>
    <script>
        var current_page;
        var page = 1;

        function list() {
            $(".top-main h2").fadeOut(100, function() {
                $(this).html("Movies list").fadeIn(100);
            });
            $("#add-movie").hide();
            $("#settings").hide();
            $("#movies-list").slideDown(300);
            current_page = 'list';
            load_movies();
        }

        function add() {
            $(".top-main h2").fadeOut(100, function() {
                $(this).html("Add movie").fadeIn(100);
            });
            $("#movies-list").hide();
            $("#settings").hide();
            $("#add-movie").slideDown(300);
            current_page = 'add';
        }

        function settings() {
            $(".top-main h2").fadeOut(100, function() {
                $(this).html("Admin Settings").fadeIn(100);
            });
            $("#movies-list").hide();
            $("#add-movie").hide();
            $("#settings").slideDown(300);
            current_page = 'settings';
        }

        //add movie functions
        function search() {
            if (page == 1) {
                $("#load_more").hide();
                $("#search_results").html("<div class='loader'>Loading...</div>");
            }
            cancel_movie();
            query = document.movie_search.search_query.value;
            sort_by = $("input[name='sort_by']:checked").val();
            order_by = $("input[name='order_by']:checked").val();
            limit = 20;
            $.getJSON('https://yts.mx/api/v2/list_movies.json?query_term=' + query + '&page=' + page + '&limit=' + limit + "&sort_by=" + sort_by + '&order_by=' + order_by,
                function(data) {
                    if (data.data.movie_count == 0) {
                        clear_results();
                        no_result();
                        return;
                    }
                    array = data.data.movies;
                    movies = "";
                    var i = 0;
                    for (i in array) {
                        movies += "<div class='movie-card-outer'><div onmouseenter=\"$(this).children('.add_movie').show();\" onmouseleave=\"$(this).children('.add_movie').hide();\" class='movie-card'><img class='poster' src='" + array[i].medium_cover_image + "'/><h3 class='title'>" + array[i].title + "</h3><div class='year'>" + array[i].year + "</div><div class='add_movie'><button onclick='add_movie(" + array[i].id + ")'>Add</button></div></div></div>";
                    }
                    if (i < limit - 1) $("#load_more").hide();
                    if (page == 1) $("#search_results").html("");
                    if (page == 1 && i == limit - 1) $("#load_more").show();
                    $("#search_results").append(movies);
                    if (i > 0) $(".clear_results").show();
                });
        }

        function load_more() {
            page++;
            search();
        }

        function no_result() {
            $("#search_results").html("<div class='no_result'><h4>No Result !</h4></div>");
        }

        function clear_results() {
            $("#search_results").html("");
            $(".clear_results").hide();
            $("#load_more").hide();
        }

        function add_movie(id) {
            clear_results();
            $.getJSON("https://yts.mx/api/v2/movie_details.json?movie_id=" + id, function(data) {
                M = data.data.movie;
                imdb_code = M.imdb_code;
                title = M.title;
                year = M.year;
                rating = M.rating;
                genres = M.genres;
                poster = M.medium_cover_image;
                language = M.language;
                summary = M.description_intro;
                var url;
                var quality;
                $("form[name='movie_form'] img").attr("src", poster);
                $("form[name='movie_form'] img").attr("title", title);
                $("form[name='movie_form'] img").attr("alt", title);
                $("form[name='movie_form'] input[name='imdb_code']").val(imdb_code);
                $("form[name='movie_form'] input[name='poster']").val(poster);
                $("form[name='movie_form'] input[name='title']").val(title);
                $("form[name='movie_form'] input[name='year']").val(year);
                $("form[name='movie_form'] input[name='rating']").val(rating);
                $("form[name='movie_form'] input[name='genres']").val(genres.join(", "));
                $("form[name='movie_form'] textarea[name='summary']").html(summary);
                $.getScript('../scripts/languages.js', function() {
                    language = isoLangs[language];
                    $("form[name='movie_form'] input[name='language']").val(language.name + " (" + language.nativeName + ")");
                });
                $("form[name='movie_form']").show();
                autosize($("form[name='movie_form'] textarea[name='summary']"));
            });
        }

        function cancel_movie() {
            $("form[name='movie_form']").hide();
        }

        //on hash change
        $(window).on('hashchange', function() {
            if (window.location.hash == "#add" && current_page != 'add') add();
            else if (window.location.hash == "#list" && current_page != 'list') list();
            else if (window.location.hash == "#settings" && current_page != 'settings') settings();
        });

        //document ready
        $(document).ready(function() {
            if (window.location.hash == "#add") add();
            else if (window.location.hash == "#settings") settings();
            else list();
            $('header nav ul li').hover(
                function() {
                    $(this).children(".info").fadeIn();
                },
                function() {
                    $(this).children(".info").fadeOut();
                });
        });

        //list movies functions
        function load_movies(selector = '') {
            //title, page, genre, year, order_by, sort_by
            $.getJSON("get_movies.php?" + selector, function(json) {
                list_movies = '';
                movies = json.movies;
                for (i in movies) {
                    genres = movies[i].genres.split(", ");
                    list_movies += "<div class='movie-card'>";
                    list_movies += "<div class='poster'><img src='../imgs/movies posters/" + movies[i].poster + "'></div>";
                    list_movies += "<div class='movie-details'>";
                    list_movies += "<h4>" + movies[i].title + "</h4>";
                    list_movies += "<div class='section'>";
                    list_movies += "<a href='#' class='year' onclick='load_movies(\"year=" + movies[i].year + "\")'>" + movies[i].year + "</a>";
                    list_movies += "<strong class='genre'>";
                    for (j in genres) list_movies += "<a href='#' onclick='load_movies(\"genre=" + genres[j] + "\")'>" + genres[j] + "</a>";
                    list_movies += "</strong>";
                    list_movies += "</div>";
                    list_movies += "<div class='buttons'>";
                    list_movies += "<button name='update_url' onclick='update_url(\"" + movies[i].imdb_code + "\")'>Update movie URL</button>";
                    list_movies += "<button name='delete' onclick='delete_movie(\"" + movies[i].imdb_code + "\")'>Delete</button>";
                    list_movies += "</div>";
                    list_movies += "</div>";
                    list_movies += "</div>";
                }
            }).done(function() {
                $("#movies-list #content").html(list_movies);
            });
        }

        function update_url(code) {
            $.getJSON("movie_info.php?imdb_code=" + code, function(data) {
                $("#update_url p,#update_url strong").hide();
                $("#update_url h4").html(data.content.title + " (" + data.content.year + ")");
                $("#update_url input[name='url']").val(data.content.movie_url);
                $("#update_url input[name='imdb_code']").val(code);
                $("#update_url").show();
                $("#update_url form").slideDown();
            });
        }

        function delete_movie(code) {
            $("#delete_movie input[name='imdb_code']").val(code);
            $("#delete_movie").show();
            $("#delete_movie form").slideDown();
        }
        //show text letter by letter
        var inputLetterByLetter = function(input, message, index = 0, interval = 80) {
            if (index == 0) $(input).val("");
            if (index < message.length) {
                $(input).val($(input).val() + message[index++]);
                setTimeout(function() {
                    inputLetterByLetter(input, message, index, interval);
                }, interval);
            }
        }

        //Submits
        $(function() {
            $('form[name=movie_form]').submit(function() {
                $("form[name=movie_form]").hide();
                $("#search_results").html("<div class='loader'>Loading...</div>");
                $.post($(this).attr('action'), $(this).serialize(), function(json) {
                    if (json.message == 'Success') {
                        $("#search_results").html("<div class='success'><strong>" + json.message + ": </strong>" + json.details + "</div>");
                    } else {
                        $("#search_results").html("<div class='failed'><strong>" + json.message + ": </strong>" + json.details + "</div>");
                    }
                }, 'json');
                return false;
            });
        });
        $(function() {
            $('#update_url form').submit(function() {
                $.post("?", $(this).serialize(), function(json) {
                    $("#update_url form").hide();
                    if (json.message == 'success') {
                        $("#update_url strong").show();
                    } else {
                        $("#update_url p").html("<b style='color:red;'>" + json.message + ": </b>" + json.details);
                    }
                    setTimeout(function() {
                        $("#update_url").hide()
                    }, 1000);
                }, 'json');
                return false;
            });
        });
        $(function() {
            $('#delete_movie form').submit(function() {
                $.post("?", $(this).serialize(), function(json) {
                    $("#delete_movie form").hide();
                    if (json.message == 'success') {
                        $("#delete_movie p").html("Done");
                    } else {
                        $("#delete_movie p").html("<b style='color:red;'>" + json.message + ": </b>" + json.details);
                    }
                    $("#delete_movie p").show();
                    setTimeout(function() {
                        $("#delete_movie").hide()
                    }, 2000);
                    setTimeout(function() {
                        list();
                    }, 2500);
                }, 'json');
                return false;
            });
        });
        $(function() {
            $("form#settings_form").submit(function() {
                $("form#settings_form > input[type='submit']").attr("disabled", true);
                $.post($(this).attr("action"), $(this).serialize(), function(json) {
                    if (json.message == "Success") color = "#5d5";
                    else color = "#d55";
                    message = "<p style='position:fixed; line-height:60px; top:50%; left:50%; color:" + color + "; padding:0 15px; transform:translate(-50%,-50%); background:#fff; font-size:20px; border-radius:3px; border:2px solid; z-index:1;'><b>" + json.message + ": </b>" + json.details + "</p>";
                }, "json").done(function() {
                    $("#settings").append(message);
                    setTimeout(function() {
                        $("#settings p").remove();
                        $("form#settings_form > input[type='submit']").attr("disabled", false);
                    }, 2000);
                });
                return false;
            });
        });
        $(function() {
            $("#password_form form").submit(function() {
                $("#password_form form input[type='submit']").attr("disabled", true);
                if ($("#password_form form input[name='New_password").val() != $("#password_form form input[name='Confirm_password").val()) {
                    message = "<p style='position:fixed; line-height:60px; top:50%; left:50%; color:#d55; padding:0 15px; transform:translate(-50%,-50%); background:#fff; font-size:20px; border-radius:3px; border:2px solid; z-index:1;'><b>Password Error: </b>Confirme the new password</p>";
                    $("#settings").append(message);
                    setTimeout(function() {
                        $("#settings p").remove();
                        $("#password_form form input[type='submit']").attr("disabled", false);
                    }, 2000);
                    return false;
                }
                $.post($(this).attr("action"), $(this).serialize(), function(json) {
                    if (json.message == "Success") color = "#5d5";
                    else color = "#d55";
                    message = "<p style='position:fixed; line-height:60px; top:50%; left:50%; color:" + color + "; padding:0 15px; transform:translate(-50%,-50%); background:#fff; font-size:20px; border-radius:3px; border:2px solid; z-index:1;'><b>" + json.message + ": </b>" + json.details + "</p>";
                }, "json").done(function() {
                    $("#settings").append(message);
                    setTimeout(function() {
                        $("#settings p").remove();
                        $("#password_form form input[type='submit']").attr("disabled", false);
                        if (color == "#5d5") {
                            $("#password_form form input[type='password']").val("");
                            $("#password_form, #password_form form").hide();
                        }
                    }, 2000);
                });
                return false;
            });
        });
    </script>
</head>

<body>
    <div id="wrapper">
        <header>
            <div class='top-header'>
                <h1>Dashboard</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="#list" onclick='list()'><i class="fas fa-align-justify"></i></a>
                        <div class='info' style='display:none;'>Movies list</div>
                    </li>
                    <li><a href="#add" onclick='add()'><i class="fas fa-plus"></i></a>
                        <div class='info' style='display:none;'>Add movie</div>
                    </li>
                    <li style='float:right;'><a style='width:auto;line-height:20px;padding:15px;background:#131313;color:#f7be10;' href="logout.php">Log out <i class="fas fa-arrow-right"></i></a></li>
                    <li style='float:right;'><a href='../' target='_blank'><i class="fas fa-home"></i></a>
                        <div class='info' style='display:none;'>Home Page</div>
                    </li>
                    <li style='float:right;'><a href="#settings" onclick='settings()'><i class="fas fa-cog"></i></a>
                        <div class='info' style='display:none;'>Settings</div>
                    </li>
                </ul>
                <nav>
        </header>
        <main>
            <div class='top-main'>
                <div>
                    <h2></h2>
                </div>
            </div>
            <div class="grid" id="movies-list">
                <!--?php
$string = file_get_contents("http://".$_SERVER['HTTP_HOST']."/admin/get_movies.php");
$movies = json_decode($string, true)["movies"];
foreach ($movies as $movie){
	$genres = explode(", ", urldecode($movie["genres"]));
    echo 
	"<div class='movie-card'>
		<div class='poster'><img src='../imgs/movies posters/".$movie['poster']."'></div>
		<div class='movie-details'>
			<h4>".urldecode($movie['title'])."</h4>
			<div class='section'>
				<a href='#' class='year'>".$movie['year']."</a>
				<strong class='genre'>";
				foreach ($genres as $genre){echo "<a href='#'>$genre</a> ";}
		echo	"</strong>
			</div>
			<div class='buttons'>
				<button name='update_url' onclick='update_url(\"".$movie['imdb_code']."\")'>Update movie URL</button>
				<button name='delete' onclick='delete_movie(\"".$movie['imdb_code']."\")'>Delete</button>
			</div>
		</div>
	</div>";
}
?-->
                <div id='float_search'><i class='fas fa-search' onclick='float_search_click();'></i><input type='text' oninput='load_movies("title="+btoa($(this).val()))' /></div>
                <div id='content'></div>
            </div>
            <div id='add-movie'>
                <div class='clear_results'>
                    <button onclick='clear_results()'><i class="fas fa-times-circle"></i></button>
                    <span>Clear results</span>
                </div>
                <form name='movie_search' onsubmit='page=1; search(); return false;' autocomplete='off'>
                    <input type='text' name='search_query' placeholder='Movie name'>
                    <input type='submit' name='search_active' value='Search'>
                    <div class='options'>
                        <strong>Sort by: </strong>
                        <strong><input type="radio" id="year" value='year' name='sort_by' checked> <label for="year">Year</label></strong>
                        <strong><input type="radio" id="title" value='title' name='sort_by'> <label for="title">Title</label></strong>
                        <strong><input type="radio" id="rating" value='rating' name='sort_by'> <label for="rating">Rating</label></strong>
                    </div>
                    <div class='options'>
                        <strong>Order by: </strong>
                        <strong><input type="radio" id="asc" value='asc' name='order_by'> <label for="asc">Ascending</label></strong>
                        <strong><input type="radio" id="desc" value='desc' name='order_by' checked> <label for="desc">Descending</label></strong>
                    </div>
                </form>
                <div id='search_results'></div>
                <div id='load_more'>
                    <input type='button' name='load_more_button' onclick='load_more()' value='Load More'>
                </div>
                <form name='movie_form' method='post' action='test.php' autocomplete='off' onsubmit='return false;'>
                    <div class="poster"><img></div>
                    <input type="hidden" name="imdb_code">
                    <input type="hidden" name="poster">
                    <table>
                        <tbody>
                            <tr>
                                <td><label for="title">Movie title :</label></td>
                                <td><input type="text" name="title" placeholder="Movie title"></td>
                            </tr>
                            <tr>
                                <td><label for="year">Year :</label></td>
                                <td><input type="number" name="year" placeholder="Year"></td>
                            </tr>
                            <tr>
                                <td><label for="rating">Rating :</label></td>
                                <td><input type="number" step="0.01" name="rating" placeholder="Rating"></td>
                            </tr>
                            <tr>
                                <td><label for="genres">Genres :</label></td>
                                <td><input readonly="" type="text" name="genres" placeholder="Genres"></td>
                            </tr>
                            <tr>
                                <td><label for="language">Language :</label></td>
                                <td><input type="text" name="language" value="" placeholder="Language"></td>
                            </tr>
                            <tr>
                                <td><label for="summary">Summary :</label></td>
                                <td>
                                    <textarea name="summary" placeholder="Summary"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td><label for="url">Url :</label></td>
                                <td><input name="url" value="Movies/" type="text" placeholder="url"></td>
                            </tr>
                            <tr>
                                <td><label for="quality">Quality :</label></td>
                                <td>
                                    <select name="quality">
                                        <option value="1080">1080p</option>
                                        <option value="720">720p</option>
                                        <option value="480">480p</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td style="width:100%;"><input type="submit" value="Add movie to database"></td>
                            </tr>
                            <tr>
                                <td style="width:100%;"><input type="button" value="Cancel" onclick="cancel_movie()"></td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div id='update_url'>
                <form>
                    <h4>Movie title</h4>
                    <input type="hidden" name="imdb_code">
                    <input type='text' placeholder='url (direct video url)' name='url'>
                    <input type='submit' value='Update'>
                    <i onclick='$("#update_url, #update_url form").hide()' class='fas fa-times'></i>
                </form>
                <strong>Done!</strong>
                <p></p>
            </div>
            <div id='delete_movie'>
                <form>
                    <input type="hidden" name="imdb_code">
                    <h4>Sure wanna delete this movie?</h4>
                    <input type='submit' value='Yes'>
                    <input type='reset' value='Non' onclick='$("#delete_movie, #delete_movie form").hide()'>
                </form>
                <p></p>
            </div>
            <div id='settings'>
                <form autocomplete='off' id='settings_form' name='settings_form' method='post' action='update_admin.php' onsubmit='return false'>
                    <label>First Name: </label><input type='text' name='F_name' value='<?php echo $_SESSION['F_name']; ?>' placeholder='First Name' />
                    <label>Last Name: </label><input type='text' name='L_name' value='<?php echo $_SESSION['L_name']; ?>' placeholder='Last Name' />
                    <label>Username: </label><input type='text' name='Username' value='<?php echo $_SESSION['Username']; ?>' placeholder='Username' />
                    <label>Email: </label><input type='email' name='Email' value='<?php echo $_SESSION['Email']; ?>' placeholder='Email' />
                    <label>Password: </label><input type='button' value='Change Password' onclick='$("#password_form").show(); $("#password_form form").slideDown();' />
                    <input type='submit' value='Save Changes' onclick='' style='width:100%;margin-top:10px;background:#5d5;color:#fff;font-weight:600;cursor:pointer;' />
                </form>
                <div id='password_form'>
                    <form autocomplete='off' name='password_form' method='post' action='update_admin.php' onsubmit='return false'>
                        <label>Current Password: </label><input type='password' name='Current_password' placeholder='••••••••' required />
                        <label>New Password: </label><input type='password' name='New_password' placeholder='••••••••' required />
                        <label>Confirm Password: </label><input type='password' name='Confirm_password' placeholder='••••••••' required />
                        <input type='reset' value='Cancel' onclick='$("#password_form, #password_form form").hide();' /><input type='submit' value='Change' style='background:#5d5;color:#fff;font-weight:600;' />
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>

</html>