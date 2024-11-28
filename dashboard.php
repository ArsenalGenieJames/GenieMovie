<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$api_key = "5311371d6c5f1bf83718e50f58f8f076"; // Your API key

// Check if a search query has been submitted
$search_query = isset($_GET['search']) ? $_GET['search'] : ''; // Get search query from URL (if any)
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Get the page number (default to 1)

if ($search_query) {
    // If there's a search query, fetch movies and series based on the search
    $api_url_movies = "https://api.themoviedb.org/3/search/movie?api_key=$api_key&query=" . urlencode($search_query) . "&page=$page&language=en-US";
    $api_url_series = "https://api.themoviedb.org/3/search/tv?api_key=$api_key&query=" . urlencode($search_query) . "&page=$page&language=en-US";
} else {
    // If no search query, fetch popular movies and latest TV series
    $api_url_movies = "https://api.themoviedb.org/3/movie/popular?api_key=$api_key&language=en-US&page=$page";
    $api_url_series = "https://api.themoviedb.org/3/tv/popular?api_key=$api_key&language=en-US&page=$page";
}

// Function to fetch data from API
function fetchData($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

$movies = fetchData($api_url_movies);
$series = fetchData($api_url_series);

// Check if we received any data
$movie_data = isset($movies['results']) ? $movies['results'] : [];
$series_data = isset($series['results']) ? $series['results'] : [];

// Function to get movie trailer
function getMovieTrailer($movie_id)
{
    global $api_key;
    $url = "https://api.themoviedb.org/3/movie/$movie_id/videos?api_key=$api_key&language=en-US";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);

    $video_data = json_decode($response, true);

    if (isset($video_data['results'][0])) {
        return $video_data['results'][0]['key']; // Return the first video key
    }

    return null; // No trailer found
}

// Function to get series trailer
function getSeriesTrailer($series_id)
{
    global $api_key;
    $url = "https://api.themoviedb.org/3/tv/$series_id/videos?api_key=$api_key&language=en-US";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);

    $video_data = json_decode($response, true);

    if (isset($video_data['results'][0])) {
        return $video_data['results'][0]['key']; // Return the first video key
    }

    return null; // No trailer found
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Movie & Series Dashboard</title>
    <link href="style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />
</head>
<style>
    .movie-card,
    .series-card {
        margin-bottom: 20px;
    }

    .movie-card img,
    .series-card img {
        width: 100%;
        height: auto;
    }

    .iframe-container {
        position: relative;
        padding-bottom: 56.25%;
        height: 0;
        overflow: hidden;
        max-width: 100%;
        height: auto;
    }

    .iframe-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
</style>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-body-tertiary">
        <!-- Container wrapper -->
        <div class="container-fluid">
            <!-- Toggle button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Collapsible wrapper -->
            <div class="collapse navbar-collapse" id="navbarSupportedContent">

                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <p class="nav-item mt-2" id="datetime"></p>
                    </li>
                    
                    <li class="nav-item">
                        <a href="index.php" class="btn btn-outline-primary">Logout</a>
                    </li>
                </ul>
            </div>


        </div>
        <!-- Container wrapper -->
    </nav>
    <!-- Navbar -->


    <div class="container mt-5"

        <!-- Search Form -->
        <form method="GET" action="" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Search for movies or series..." name="search" value="<?php echo htmlspecialchars($search_query); ?>">
                <button class="btn btn-primary" type="submit">Search</button>
            </div>
        </form>

        <!-- Latest Movies Section -->
        <h2>Latest Movies</h2>
        <div class="row">
            <?php if (count($movie_data) > 0): ?>
                <?php foreach ($movie_data as $movie): ?>
                    <?php
                    $trailer_key = getMovieTrailer($movie['id']); // Get trailer key for each movie
                    ?>
                    <div class="col-md-3">
                        <div class="card movie-card">
                            <img src="https://image.tmdb.org/t/p/w500<?php echo $movie['poster_path']; ?>" class="card-img-top" alt="<?php echo $movie['title']; ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $movie['title']; ?></h5>
                                <p class="card-text"><?php echo substr($movie['overview'], 0, 100) . '...'; ?></p>

                                <?php if ($trailer_key): ?>
                                    <div class="iframe-container">
                                        <iframe src="https://www.youtube.com/embed/<?php echo $trailer_key; ?>" frameborder="0" allowfullscreen></iframe>
                                    </div>
                                <?php else: ?>
                                    <p>No trailer available</p>
                                <?php endif; ?>

                                <a href="https://www.themoviedb.org/movie/<?php echo $movie['id']; ?>" class="btn btn-primary mt=2" target="_blank">View More</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No movies found.</p>
            <?php endif; ?>
        </div>

        <!-- Latest TV Series Section -->
        <h2>Latest TV Series</h2>
        <div class="row">
            <?php if (count($series_data) > 0): ?>
                <?php foreach ($series_data as $series): ?>
                    <?php
                    $trailer_key = getSeriesTrailer($series['id']); // Get trailer key for each series
                    ?>
                    <div class="col-md-3">
                        <div class="card series-card">
                            <img src="https://image.tmdb.org/t/p/w500<?php echo $series['poster_path']; ?>" class="card-img-top" alt="<?php echo $series['name']; ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $series['name']; ?></h5>
                                <p class="card-text"><?php echo substr($series['overview'], 0, 100) . '...'; ?></p>

                                <?php if ($trailer_key): ?>
                                    <div class="iframe-container">
                                        <iframe src="https://www.youtube.com/embed/<?php echo $trailer_key; ?>" frameborder="0" allowfullscreen></iframe>
                                    </div>
                                <?php else: ?>
                                    <p>No trailer available</p>
                                <?php endif; ?>

                                <a href="https://www.themoviedb.org/tv/<?php echo $series['id']; ?>" class="btn btn-primary" target="_blank">View More</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No series found.</p>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <div class="mt-3">
            <?php if ($page > 1): ?>
                <a href="?search=<?php echo urlencode($search_query); ?>&page=<?php echo $page - 1; ?>" class="btn btn-secondary">Previous</a>
            <?php endif; ?>
            <a href="?search=<?php echo urlencode($search_query); ?>&page=<?php echo $page + 1; ?>" class="btn btn-secondary">Next</a>
        </div>
    </div>


</body>
<script>
    function updateDateTime() {
        const now = new Date();
        const datetimeStr = now.toLocaleString();
        document.getElementById('datetime').textContent = datetimeStr;
    }
    setInterval(updateDateTime, 1000); // Update every second updateDateTime(); // Initial call 
</script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/mdb-ui-kit@5.1.1/dist/mdb.min.js"></script>


</html>