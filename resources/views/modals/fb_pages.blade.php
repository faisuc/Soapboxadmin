<div class="modal fade fbpages-modal" tabindex="-1" role="dialog" aria-labelledby="fbpages-modal" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">FB Pages</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="post-notes-container">
                    @forelse ($pages as $page)    
                    @empty
                    @endforelse
                    <?php
                    foreach ($pages as $page_key => $page) {
                        echo "<pre>";
                        print_r($page);
                        echo "</pre>";
                    }
                    ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>