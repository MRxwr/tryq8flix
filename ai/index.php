<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="AI Art Generator: Transform your imagination into stunning art. Create masterpieces with ease using our advanced AI models.">
    <meta name="keywords" content="AI Art Generator, AI Art, Create Art, AI Models, Art Generator">
    <meta name="author" content="TRYQ8FLiX">
    <meta http-equiv="Cache-Control" content="max-age=31536000, must-revalidate">
    <meta http-equiv="Expires" content="Tue, 15 Apr 2026 00:00:00 GMT">
    <title>AI Art Generator - Create Stunning Art with AI</title>
    <link rel="icon" href="images/mobileLogo.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css" media="print" onload="this.media='all'">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#ffffff">
  </head>
  <body>
    <h1 class="page-title">AI Art Generator</h1>
    <h6 class="page-subtitle">Transform Your Imagination Into Stunning Art</h6>
    <div class="container-fluid">
        <div class="row text-center generator-card">
          <div class="col-12 mb-3">
            <h3 class="text-center mb-4"><i class="fas fa-paint-brush mr-2"></i>Create Your Masterpiece</h3>
          </div>
            <div class="col-12 mb-3">
                <div class="input-icon-wrapper">
                    <i class="fas fa-magic input-icon"></i>
                    <input name="prompt" id="prompt" value="" class="form-control icon-input" placeholder="Describe your art (e.g., a futuristic city with flying cars)" autocomplete="off">
                </div>
            </div>
            
            <div class="col-md-6 col-12 pt-2">
                <p class="text-left mb-1"><i class="fas fa-arrows-alt-h mr-1"></i> Width</p>
                <input type="number" id="imgWidth" class="form-control" placeholder="Width (e.g. 1080)" min="256" max="2048" value="1080">
            </div>
            <div class="col-md-6 col-12 pt-2">
                <p class="text-left mb-1"><i class="fas fa-arrows-alt-v mr-1"></i> Height</p>
                <input type="number" id="imgHeight" class="form-control" placeholder="Height (e.g. 1080)" min="256" max="2048" value="1080">
            </div>
            
            <div class="col-12 pt-4 text-center">
                <p class="feature-title"><i class="fas fa-robot mr-1"></i> Select Model <span class="tooltip-toggle" data-toggle="tooltip" title="Normal: Better quality but slower. Fast: Quick generation with good quality."><i class="fas fa-question-circle"></i></span></p>
                <button class="btn btn-outline-light model-btn" data-model="normal">Normal</button>
                <button class="btn btn-outline-light model-btn" data-model="turbo">Fast</button>
            </div>
            
            <div class="col-12 pt-4">
                <div class="row justify-content-center">
                    <div class="col-md-6 col-12 mb-2">
                        <button id="submitBtn" class="btn btn-primary w-100"><i class="fas fa-bolt mr-2"></i> Generate Image</button>
                    </div>
                    <div class="col-md-6 col-12 mb-2">
                         <button id="refreshBtn" class="btn btn-primary w-100" disabled><i class="fas fa-sync-alt mr-1"></i> Refresh</button>
                    </div>
                </div>
            </div>
            
            <div id="toastContainer" class="col-12 pt-2 text-center"></div>
            
            <div class="col-12"><hr class="section-divider"></div>
            
            <div class="col-12 pt-3" id="returnImage">
                <img id="frame" src="" alt="Your generated image will appear here" width="100%" height="350">
            </div>
            
            <div class="col-12 pt-4">
                <div class="row">
                    <div class="col-md-6 col-12 mb-2">
                        <button id="downloadBtn" class="btn btn-success w-100" disabled><i class="fas fa-download mr-1"></i> Download</button>
                    </div>
                    <div class="col-md-6 col-12 mb-2">
                        <button id="shareBtn" class="btn btn-warning w-100" disabled><i class="fas fa-share-alt mr-1"></i> Share</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-12 mb-2">
                        <button id="savePromptBtn" class="btn btn-info w-100" disabled><i class="fas fa-bookmark mr-1"></i> Save Prompt</button>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-6 col-12 mb-2">
                        <button id="viewHistoryBtn" class="btn btn-secondary w-100"><i class="fas fa-history mr-1"></i> Prompts History</button>
                    </div>
                    <div class="col-md-6 col-12 mb-2">
                        <button id="clearCookiesBtn" class="btn btn-danger w-100"><i class="fas fa-trash-alt mr-1"></i> Clear Prompts</button>
                    </div>
                </div>
                
                <div id="historyList" class="mt-3"></div>
            </div>
        </div>
    </div>
    
    <footer>
        <p>Share this website:</p>
        <div class="mb-3">
            <a href="https://www.facebook.com/sharer/sharer.php?u=https://tryq8flix.com/aiGenerator.php" target="_blank" aria-label="Share on Facebook"><i class="fab fa-facebook-square fa-2x"></i></a>
            <a href="https://twitter.com/intent/tweet?url=https://tryq8flix.com/aiGenerator.php" target="_blank" aria-label="Share on Twitter"><i class="fab fa-twitter-square fa-2x"></i></a>
            <a href="https://wa.me/?text=Check out this amazing AI Art Generator: https://tryq8flix.com/aiGenerator.php" target="_blank" aria-label="Share on WhatsApp"><i class="fab fa-whatsapp-square fa-2x"></i></a>
        </div>
        <p>Courtesy of <a href="https://tryq8flix.com" target="_blank"><img src="images/mobileLogo.png" alt="TRYQ8FLiX Logo" style="width: 20px; height: 20px;"></a>
        </p>
    </footer>
    
    <div id="loading">
        <div class="spinner"></div>
        <p>Creating Your Masterpiece... Please Wait</p>
    </div>
    <script src="js/script.obfuscated.js" type="text/javascript" crossorigin="anonymous" integrity="sha512-uydU5/LIetAVsLRkNmePfMY9EL/O1/5MbghbU+y2X6kvWC27+5p4Irfw050vLyY/P4PpqEcetnq/jRAcPDGFQg==" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
          navigator.serviceWorker.register('/service-worker.js').then(function(registration) {
            console.log('ServiceWorker registration successful with scope: ', registration.scope);
          }, function(err) {
            console.log('ServiceWorker registration failed: ', err);
          });
        });
      }
    </script>
    <script>
      let deferredPrompt;
      const installButton = document.createElement('button');
      installButton.textContent = 'Install App';
      installButton.style.position = 'fixed';
      installButton.style.bottom = '20px';
      installButton.style.right = '20px';
      installButton.style.padding = '10px 20px';
      installButton.style.backgroundColor = '#007bff';
      installButton.style.color = '#fff';
      installButton.style.border = 'none';
      installButton.style.borderRadius = '5px';
      installButton.style.cursor = 'pointer';
      installButton.style.display = 'none';
      document.body.appendChild(installButton);

      if (window.matchMedia('(display-mode: standalone)').matches || navigator.standalone) {
        console.log('App is already installed');
      } else {
        window.addEventListener('beforeinstallprompt', (e) => {
          e.preventDefault();
          deferredPrompt = e;
          installButton.style.display = 'block';
        });

        installButton.addEventListener('click', () => {
          installButton.style.display = 'none';
          if (deferredPrompt) {
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then((choiceResult) => {
              if (choiceResult.outcome === 'accepted') {
                console.log('User accepted the install prompt');
              } else {
                console.log('User dismissed the install prompt');
              }
              deferredPrompt = null;
            });
          }
        });
      }
    </script>
  </body>
</html>