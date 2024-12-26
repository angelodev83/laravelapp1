<style>
  .scrollable-content {
    width: 100%; /* Adjust width as needed */
    height: 500px; /* Adjust height as needed */
    overflow-y: auto;
    /* border: 1px solid #ccc; */
    padding: 10px;
  }

  .commentary-section {
    width: 100%; /* Adjust width as needed */
    height: 450px; /* Adjust height as needed */
    overflow-y: auto;
    /* border: 1px solid #ccc; */
    padding: 10px;
  }

  /* .comment-card {
    margin-bottom: 10px;
    padding: 10px;
    border: 1px solid #ccc;
    background-color: #f9f9f9;
  }

  .comment-input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    resize: none;
  } */

  /* @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700');
 *{
   border-radius: 0px !important;
   border: 0px !important;
 } */

 /* body{
   margin: 0px;
   font-family: "Poppins", sans-serif !important;
   background: #FED18C !important;
 } */

 /* .height-vh{
   height: 100px;
 } */

#comment-container{
  padding: 10px !important;
  background: white;
  box-shadow: rgba(50, 50, 93, 0.25) 0px 6px 12px -2px, rgba(0, 0, 0, 0.3) 0px 3px 7px -3px;
}

.date{
  font-size: 12px;
}

.comment-text{
  font-size: 14px;
  line-height: 1.2rem;
}

.fs-14{
  font-size: 14px;
}

.name{
  color: #212529;
}

.cursor{
  cursor: pointer;
}

.cursor:hover{
  color: blue;
}

</style>
</head>
<body>



<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" id="exampleModalLabel">Horizontal Scrollable Divs</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-lg-8 col-md-12">
            <div class="scrollable-content">
              <h2>Scrollable Div 1</h2>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer ut vehicula justo, id ultricies lorem. Donec fringilla ligula nec ex dapibus, at hendrerit turpis dapibus. Curabitur non massa at turpis suscipit ultricies.</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer ut vehicula justo, id ultricies lorem. Donec fringilla ligula nec ex dapibus, at hendrerit turpis dapibus. Curabitur non massa at turpis suscipit ultricies.</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer ut vehicula justo, id ultricies lorem. Donec fringilla ligula nec ex dapibus, at hendrerit turpis dapibus. Curabitur non massa at turpis suscipit ultricies.</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer ut vehicula justo, id ultricies lorem. Donec fringilla ligula nec ex dapibus, at hendrerit turpis dapibus. Curabitur non massa at turpis suscipit ultricies.</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer ut vehicula justo, id ultricies lorem. Donec fringilla ligula nec ex dapibus, at hendrerit turpis dapibus. Curabitur non massa at turpis suscipit ultricies.</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer ut vehicula justo, id ultricies lorem. Donec fringilla ligula nec ex dapibus, at hendrerit turpis dapibus. Curabitur non massa at turpis suscipit ultricies.</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer ut vehicula justo, id ultricies lorem. Donec fringilla ligula nec ex dapibus, at hendrerit turpis dapibus. Curabitur non massa at turpis suscipit ultricies.</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer ut vehicula justo, id ultricies lorem. Donec fringilla ligula nec ex dapibus, at hendrerit turpis dapibus. Curabitur non massa at turpis suscipit ultricies.</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer ut vehicula justo, id ultricies lorem. Donec fringilla ligula nec ex dapibus, at hendrerit turpis dapibus. Curabitur non massa at turpis suscipit ultricies.</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer ut vehicula justo, id ultricies lorem. Donec fringilla ligula nec ex dapibus, at hendrerit turpis dapibus. Curabitur non massa at turpis suscipit ultricies.</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer ut vehicula justo, id ultricies lorem. Donec fringilla ligula nec ex dapibus, at hendrerit turpis dapibus. Curabitur non massa at turpis suscipit ultricies.</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer ut vehicula justo, id ultricies lorem. Donec fringilla ligula nec ex dapibus, at hendrerit turpis dapibus. Curabitur non massa at turpis suscipit ultricies.</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer ut vehicula justo, id ultricies lorem. Donec fringilla ligula nec ex dapibus, at hendrerit turpis dapibus. Curabitur non massa at turpis suscipit ultricies.</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer ut vehicula justo, id ultricies lorem. Donec fringilla ligula nec ex dapibus, at hendrerit turpis dapibus. Curabitur non massa at turpis suscipit ultricies.</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer ut vehicula justo, id ultricies lorem. Donec fringilla ligula nec ex dapibus, at hendrerit turpis dapibus. Curabitur non massa at turpis suscipit ultricies.</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer ut vehicula justo, id ultricies lorem. Donec fringilla ligula nec ex dapibus, at hendrerit turpis dapibus. Curabitur non massa at turpis suscipit ultricies.</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer ut vehicula justo, id ultricies lorem. Donec fringilla ligula nec ex dapibus, at hendrerit turpis dapibus. Curabitur non massa at turpis suscipit ultricies.</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer ut vehicula justo, id ultricies lorem. Donec fringilla ligula nec ex dapibus, at hendrerit turpis dapibus. Curabitur non massa at turpis suscipit ultricies.</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer ut vehicula justo, id ultricies lorem. Donec fringilla ligula nec ex dapibus, at hendrerit turpis dapibus. Curabitur non massa at turpis suscipit ultricies.</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer ut vehicula justo, id ultricies lorem. Donec fringilla ligula nec ex dapibus, at hendrerit turpis dapibus. Curabitur non massa at turpis suscipit ultricies.</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer ut vehicula justo, id ultricies lorem. Donec fringilla ligula nec ex dapibus, at hendrerit turpis dapibus. Curabitur non massa at turpis suscipit ultricies.</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer ut vehicula justo, id ultricies lorem. Donec fringilla ligula nec ex dapibus, at hendrerit turpis dapibus. Curabitur non massa at turpis suscipit ultricies.</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer ut vehicula justo, id ultricies lorem. Donec fringilla ligula nec ex dapibus, at hendrerit turpis dapibus. Curabitur non massa at turpis suscipit ultricies.</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer ut vehicula justo, id ultricies lorem. Donec fringilla ligula nec ex dapibus, at hendrerit turpis dapibus. Curabitur non massa at turpis suscipit ultricies.</p>
            
            </div>
          </div>
          <div class="col-lg-4 col-md-12">
            <div class="commentary-section">
              <h4>Comments</h4>
              <!-- comments -->
                
                    <div class="row d-flex justify-content-center">
                        <div class="col-md-12">
                            <div class="d-flex flex-column" id="comment-container">
                                <div class="bg-white">
                                    <div class="flex-row d-flex">
                                        <div src="" width="40" class="rounded-circle user-avatar-initials"></div>
                                        <div class="d-flex flex-column justify-content-start ml-2">
                                        <span class="d-block font-weight-bold name">Wonder Woman</span>
                                        <span class="date text-black-50">Public - 09Jun, 2021</span>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <p class="comment-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed facilisis velit lorem, et condimentum est tempus sed. Integer tristique malesuada diam at mollis. Quisque id finibus mauris. Donec turpis justo, euismod nec commodo quis, elementum nec risus. Praesent blandit in lacus sed pretium. Duis in velit augue. Integer velit urna, convallis eget fermentum sed, aliquet at quam.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            <textarea id="commentInput" class="comment-input" placeholder="Type your comment here..."></textarea>

          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
  // Get the textarea element
  const commentInput = document.getElementById('commentInput');

  // Add event listener for the keydown event
  commentInput.addEventListener('keydown', function(event) {
    // Check if Enter is pressed without the Shift key
    if (event.key === 'Enter' && !event.shiftKey) {
      // Prevent default behavior of Enter key (form submission)
      event.preventDefault();

      // Get the value of the textarea
      const commentText = commentInput.value.trim();

      // Check if the textarea is not empty
      if (commentText !== '') {
        // Create a new comment card element
        const commentCard = document.createElement('div');
        commentCard.classList.add('comment-card');
        commentCard.textContent = commentText;

        // Append the comment card to the container
        const container = document.querySelector('.commentary-section');
        container.appendChild(commentCard);

        // Scroll to the bottom of the container
        container.scrollTop = container.scrollHeight;

        // Clear the textarea
        commentInput.value = '';
      }
    }
  });
</script>
