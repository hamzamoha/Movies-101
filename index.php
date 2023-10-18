<html>

<head>
    <link rel="stylesheet" href="fonts.css" />
    <link rel="stylesheet" href="css/loading.css" />
    <style>
        * {
            padding: 0;
            margin: 0;
            border: 0;
            text-decoration: none;
            list-style: none;
            font-family: 'TitilliumWeb';
        }

        body {
            background: #0d1e28;
        }

        #wrapper {
            width: 1200px;
            margin: 0 auto;
            background: #f3f3f3;
            max-width: 100%;
        }

        header {
            background: url(imgs/slider_img.png) center no-repeat;
            background-size: cover;
        }

        header nav ul li {
            float: left;
        }

        header nav ul li a {
            display: block;
            padding: 0 10px;
            line-height: 40px;
            color: #FFF;
        }

        header nav ul {
            background: rgba(0, 0, 0, 0.65);
        }

        header nav ul:after {
            content: '';
            display: block;
            clear: both;
        }

        header nav ul li a:hover {
            color: #444;
            background: #f7be10;
            transition: .4s;
        }

        header .slide {
            text-align: center;
            background: rgba(0, 0, 0, 0.5);
            height: 280px;
            box-sizing: border-box;
            padding: 20px 0;
        }

        header h1 {
            font-size: 50px;
            line-height: 60px;
            color: #f7be10;
        }

        header p {
            text-align: center;
            margin: 0 auto;
            padding: 20px 25%;
            color: #bbbbbb;
        }

        /**/
        main {
            overflow: hidden;
        }

        /*Movies list*/
        .movies-list {
            margin: 10px;
        }

        .movies-list:after {
            clear: both;
            content: '';
            display: block;
        }

        .movies-list .movie-card {
            box-sizing: border-box;
            position: relative;
            border: solid #dcad1e 5px;
            border-radius: 5px;
        }

        .movies-list .movie-card-outer {
            width: 20%;
            height: 300px;
            float: left;
            box-sizing: border-box;
            padding: 5px;
            margin: 5px 0;
        }

        .movies-list .movie-card img.poster {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .movies-list .movie-card h3.title {
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

        .movies-list .movie-card .year {
            position: absolute;
            right: 0;
            top: 0;
            z-index: 1;
            background: #dcad1e;
            color: #4e4e4e;
            padding: 5px 10px;
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
    </style>
    <script src='https://code.jquery.com/jquery-3.5.1.min.js'></script>
    <script>
        $(window).resize(function() {
            $(".movie-card-outer").height($(".movie-card-outer").width() * 1.4);
        });

        function load_movies() {
            var array;
            limit = 10;
            genre = "";
            quality = "1080p";
            sort_by = "year";
            $.getJSON(document.location.origin + '/admin/get_movies.php?page=' + page + '&sort_by=' + sort_by + '&quality=' + quality + '&genre=' + genre + '&limit=' + limit, function(data) {
                array = data.movies;
                movies = "";
                i = 0;
                for (i in array) {
                    movies += "<div class='movie-card-outer'><a href='movie.php?imdb_code=" + array[i].imdb_code + "'><div class='movie-card'><img class='poster' src='imgs/movies posters/" + array[i].poster + "'/><h3 class='title'>" + array[i].title + "</h3><div class='year'>" + array[i].year + "</div></div></a></div>";
                }
                if (i < limit - 1) $("#load_more").hide();
                if (page == 1 && i == limit - 1) $("#load_more").show();
                if (page == 1) $(".movies-list").html("");
                $(".movies-list").append(movies);
            });
        }
        $(document).ready(function() {
            $(".movie-card-outer").height($(".movie-card-outer").width() * 1.4);
            page = 1;
            $(".movies-list").html("<div class='loader'>Loading...</div>");
            load_movies();
        });

        function load_more() {
            page++;
            load_movies();
        }
    </script>
    <title>Movies</title>
</head>

<body>
    <div id="wrapper">
        <header>
            <nav>
                <ul>
                    <li><a href="javascript:void(0)" onclick='window.location = document.location.origin'>Home</a></li>
                    <li><a href="#">About us</a></li>
                    <li><a href="javascript:void(0)" onclick='window.location = document.location.origin + "/login.php"' href="#">Log in</a></li>
                </ul>
            </nav>
            <div class='slide'>
                <h1>Movies</h1>
                <p>
                    This section is for movies that are stored in the database. You can log in and add more movies (also edit and delete), but instead of that browse those movies and check if everything is alright and have fun.
                </p>
            </div>

        </header>
        <main>
            <div class='movies-list'></div>
        </main>
        <div id='load_more'>
            <input type='button' name='load_more_button' onclick='load_more()' value='Load More'>
        </div>
        <footer>

        </footer>
    </div>
</body>

</html>