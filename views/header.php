<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TryQ8Flix</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #141414; color: #fff; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; overflow-x: hidden; }
        .navbar { background-color: transparent; transition: background-color 0.5s; padding: 15px 4%; }
        .navbar.scrolled { background-color: #141414; }
        .navbar-brand { color: #e50914; font-weight: bold; font-size: 1.8rem; }
        .nav-link { color: #e5e5e5; font-size: 0.9rem; margin-left: 15px; }
        .nav-link:hover { color: #b3b3b3; }
        
        .hero { height: 85vh; position: relative; background-size: cover; background-position: center; display: flex; align-items: center; }
        .hero-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to top, #141414 0%, transparent 60%, rgba(0,0,0,0.7) 100%); }
        .hero-content { position: relative; z-index: 2; max-width: 600px; padding-left: 4%; margin-top: 100px; }
        .hero-title { font-size: 3.5rem; font-weight: bold; margin-bottom: 1rem; text-shadow: 2px 2px 4px rgba(0,0,0,0.5); }
        .hero-desc { font-size: 1.2rem; margin-bottom: 2rem; text-shadow: 1px 1px 2px rgba(0,0,0,0.5); }
        
        .section-title { font-size: 1.4rem; font-weight: bold; margin: 2rem 0 1rem 4%; color: #e5e5e5; }
        .movie-row { display: flex; overflow-x: auto; padding: 0 4% 2rem 4%; scrollbar-width: none; }
        .movie-row::-webkit-scrollbar { display: none; }
        .movie-card { flex: 0 0 auto; width: 200px; margin-right: 10px; transition: transform 0.3s; cursor: pointer; position: relative; }
        .movie-card:hover { transform: scale(1.1); z-index: 10; }
        .movie-card img { width: 100%; height: 300px; object-fit: cover; border-radius: 4px; }
        
        .btn-netflix { background-color: #e50914; color: white; border: none; padding: 0.5rem 1.5rem; font-weight: bold; border-radius: 4px; }
        .btn-netflix:hover { background-color: #f40612; color: white; }
        .btn-secondary-netflix { background-color: rgba(109, 109, 110, 0.7); color: white; border: none; padding: 0.5rem 1.5rem; font-weight: bold; border-radius: 4px; margin-left: 10px; }
        .btn-secondary-netflix:hover { background-color: rgba(109, 109, 110, 0.4); color: white; }

        .login-container { height: 100vh; background-image: url('https://assets.nflxext.com/ffe/siteui/vlv3/f841d4c7-10e1-40af-bcae-07a3f8dc141a/f6d7434e-d6de-4185-a6d4-c77a2d08737b/US-en-20220502-popsignuptwoweeks-perspective_alpha_website_medium.jpg'); background-size: cover; display: flex; align-items: center; justify-content: center; }
        .login-card { background-color: rgba(0,0,0,0.75); padding: 60px; border-radius: 4px; width: 450px; }
        .form-control { background-color: #333; border: none; color: white; height: 50px; }
        .form-control:focus { background-color: #454545; color: white; box-shadow: none; }
        
        .modal-content { background-color: #181818; color: white; }
        .modal-header { border-bottom: none; }
        .modal-footer { border-top: none; }
        
        .live-match-card { background-color: #2f2f2f; border-radius: 8px; margin-bottom: 15px; padding: 15px; display: flex; align-items: center; justify-content: space-between; }
        .team-logo { width: 50px; height: 50px; object-fit: contain; }
        .match-time { font-weight: bold; color: #e50914; }
    </style>
</head>
<body>
<nav class="navbar fixed-top">
  <div class="container-fluid d-flex justify-content-between align-items-center">
    <a class="navbar-brand" href="?v=Home">TRYQ8FLIX</a>
    <div class="d-flex align-items-center">
        <a class="nav-link text-white me-3" href="?v=Search"><i class="fas fa-search fa-lg"></i></a>
        <a class="nav-link text-white" href="?v=Settings"><i class="fas fa-cog fa-lg"></i></a>
    </div>
  </div>
</nav>
