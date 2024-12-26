<div class="col-lg-12 d-flex">
    <div class="card radius-10 w-100">
        <div class="card-body" style="background-color: #fff; border-radius:inherit;">
            <div class="d-flex align-items-center justify-content-between">
                <h6 class="mb-1" style="color: #107F93;">
                    <i class="fa-regular fa-newspaper me-2"></i>News & Events
                </h6>
                <a href="/store/marketing/{{request()->id}}/news-and-events">
                    <button class="btn btn-sm btn-outline-primary">View More</button>
                </a>
            </div>
            <hr class="opacity-75 border-primary border-1">
            <div id="bulletin-news-and-events-recent" class="mt-1 news-and-events-list">
                @forelse($newsAndEvents as $news)
                    <div class="col">
                        <div class="card card-ne border border-primary rounded-4 overflow-hidden">
                            <div class="row g-0 h-100">
                                <div class="col-md-4 card-img-container">
                                    <!-- <img src="Storage::disk('s3')->temporaryUrl($news->file->path.$news->file->filename, now()->addMinutes(30))" alt="..." class="card-img"> -->
                                    <img height="237" width="237"  src="{{$news->storeDocuments->path}}" alt="..." class="card-img-ne">
                                </div>
                                <div class="col-md-8 d-flex flex-column">
                                    <div class="card-body card-body-ne">
                                        <h6 class="card-title">{{$news->name}}</h6>
                                        <p class="card-text">{{Str::limit($news->caption, 5300, '...')}}</p>
                                        <p class="card-text"><small class="text-muted">{{$news->updated_at->diffForHumans()}}</small>
                                        </p>
                                    </div>
                                    <div class="mt-auto mb-2 ms-3" id="btn-holder">
                                        @if($news->status_id == 802)
                                        <a href="{{$news->url}}" target="_blank" class="mt-auto btn" style="background-color: #107F93; color: #fff;">View More</a>

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
                                        <a href="javascript:;" onclick="showMore('{{ $escapedNewsData }}')" class="mt-auto btn" style="background-color: #107F93; color: #fff;">Read More</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col text-center">
                        <small class="text-secondary">No News and Events found.</small>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>