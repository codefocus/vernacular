<?php

namespace Codefocus\Vernacular\Models;

use Illuminate\Database\Eloquent\Model;
//use GuzzleHttp\Client as GuzzleClient;
//use Psr\Http\Message\ResponseInterface;
use Goose\Client as GooseClient;

class Url extends Model
{
    //  Implementing the "Learnable" trait triggers Vernacular to
    //  index the content of the attributes specified in $vernacularAttributes.
    use \Codefocus\Vernacular\Traits\Learnable;
    public $vernacularAttributes = ['content'];
    //public $vernacularTags = ['positive'];

    protected $table = 'vernacular_url';
    protected $primaryKey = 'id';

    public $timestamps = false;
    
    protected $content = false;
    
    
    
    public function getContentAttribute() {
        if (false == $this->content) {
            //  Download url and extract text content.
            $goose = new GooseClient();
            $article = $goose->extractContent($this->url);
            $this->content = $article->getCleanedArticleText();
        }
        return $this->content;
    }
    
    
}    //	class Url

