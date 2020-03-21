<!-- Modal -->
<div class="modal fade" id="sitesListModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Sites list</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @foreach($sites as $site)
                    <div class="site-wrapper">
                        <div class="form-check">
                            <input class="form-check-input site-checkbox"
                                   type="checkbox"
                                   id="site_{{$site['id']}}"
                                   data-id="{{$site['id']}}"
                                   data-matching-url="{{$site['matching_url']}}">
                            <label class="form-check-label"
                                   data-toggle="tooltip"
                                   title="{{$site['site_name']}}"
                                   for="site_{{$site['id']}}">
                                {{$site['site_name']}}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-confirm" data-dismiss="modal">Confirm</button>
                <button type="button" class="btn btn-secondary btn-close" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
