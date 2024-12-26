@foreach($newsAndEvents as $news)
<div class="col">
    <div class="card card-ne h-100">
        <div class="row g-0 h-100">
            <div class="col-md-4 card-img-container">
                <!-- <img src="Storage::disk('s3')->temporaryUrl($news->file->path.$news->file->filename, now()->addMinutes(30))" alt="..." class="card-img"> -->
                <img src="{{$news->storeDocuments->path}}" alt="..." class="card-img card-img-ne">
            </div>
            <div class="col-md-8 d-flex flex-column">
                <div class="card-body card-body-ne">
                    <h6 class="card-title">{{$news->name}}</h6>
                    <p class="card-text">{{Str::limit($news->caption, 5300, '...')}}</p>
                    <p class="card-text"><small class="text-muted">{{$news->updated_at->diffForHumans()}}</small>
                    </p>
                </div>
                <div class="ms-3 mb-3 mt-auto" id="btn-holder">
                    @if($news->status_id == 802)
                        <a href="{{$news->url}}" target="_blank" class="btn mt-auto" style="background-color: #c7caff; color: #5e17eb;">View More</a>
                    
                    @else
                        <?php
                            $newsData = json_encode([
                                'name' => $news->name,
                                'caption' => $news->caption,
                                'content' => $news->content,
                                'path' => $news->storeDocuments->path,
                                'updated_at' => $news->updated_at,
                            ]);
                            $escapedNewsData = addslashes($newsData);
                        ?>
                        <a href="javascript:;" onclick="showMore('{{ $escapedNewsData }}')" class="btn mt-auto" style="background-color: #c7caff; color: #5e17eb;">Read More</a>
                    @endif
                    @can('menu_store.marketing.news.delete')
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="deleteNews('{{ $news->id }}')" aria-label="Close"></button>
                    @endcan                
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach
 
<!-- <div class="pagination-wrapper">
    {{ $newsAndEvents->links() }}
</div> -->
