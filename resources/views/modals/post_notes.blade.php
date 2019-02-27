<div class="modal fade postnotes-modal" tabindex="-1" role="dialog" aria-labelledby="postnotes-modal" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">New Note</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <div class="post-notes-container">
                    
                </div>
              <form action="" method="post" enctype="multipart/form-data">
                <div class="form-group">
                  <label for="message-text" class="col-form-label">Note:</label>
                  <textarea class="form-control" id="message-text"></textarea>
                  <input type="hidden" name="post_id">
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary add_note">Add Note</button>
            </div>
          </div>
        </div>
      </div>