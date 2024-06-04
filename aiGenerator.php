<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">

    <title>Ai Generator</title>
  </head>
  <body>
    <h1>Try ai prompts</h1>
    <div class="container-fluid m-0 p-3">
        <div class="row m-0 text-center">
            <div class="col-8"><input name="prompt" id="prompt" value="" class="form-control"></div>
            <div class="col-4"><button id="submitBtn" class="btn btn-primary">Send</button></div>
            <div class="col-12"><iframe style="width: 100%;height: 500px;" id="frame" src=""></iframe></div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>

    <script>
        document.getElementById("prompt").addEventListener("keyup", function(event) {
                if (event.keyCode === 13) {
                    event.preventDefault();
                    document.getElementById("submitBtn").click();
                }
            });
        document.getElementById("submitBtn").addEventListener("click", function() {
            var prompt = document.getElementById("prompt").value;
            prompt = prompt.replace(/ /g, "%20");
            var frame = document.getElementById("frame");
            frame.src = "https://image.pollinations.ai/prompt/"+ prompt + "?width=2048&height=2048&nologo=true";
        });
    </script>
  </body>
</html>