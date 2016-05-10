<?php namespace Codefocus\Vernacular;


class Vernacular {
    
    
    /**
     * Index a document
     *
     */
    public function learn($document, array $tags = []) {
        $words      = [];
        $bigrams    = [];
        
        $conditionRaw       = strtolower(rtrim(trim($conditionRaw, " \r\n\t"), '.'));
        $termsRaw           = preg_split('/[ \r\n\t,;\(\)\[\]\?\#\/]+/', $conditionRaw);
        
    }
    
    
    
    public function __COPIED__()
    {
    //  Get all medical conditions from EMeds.
        $this->output->write('Fetching medical conditions from EMeds... ', false);
        
        $conditionsRaw = EMedsPatient
            ::selectRaw('[Medical Condition] AS condition')
            //->distinct()      //  We WANT duplicates.
            ->whereNotNull('Medical Condition')
            ->where('Medical Condition', '!=', '')
            ->where('Medical Condition', '!=', ' ')
            ->orderBy('Medical Condition', 'desc')
            ->lists('condition')
            //->take(10)
            ->all();
        
        $dictionaryTerms    = [];
        $dictionaryBigrams  = [];
        foreach($conditionsRaw as $conditionRaw) {
            $conditionRaw       = strtolower(rtrim(trim($conditionRaw, " \r\n\t"), '.'));
            $termsRaw           = preg_split('/[ \r\n\t,;\(\)\[\]\?\#\/]+/', $conditionRaw);
            $previousTermId2    = null;
            $previousTermId1    = null;
            $currentTermId      = null;
            foreach($termsRaw as $termRaw) {
                $term = trim($termRaw, " \r\n\t,()[]'");
                
            //  @TODO: Separate date from term.
                
                if (isset($dictionaryTerms[$term])) {
                    $dictionaryTerm = $dictionaryTerms[$term];
                }
                else {
                //  Create DictionaryTerm
                    $soundex = soundex($term);
                    $dictionaryTerm = DictionaryTerm
                        ::where('term', '=', $term)
                        ->where('soundex', '=', $soundex)
                        ->first();
                    if (!$dictionaryTerm) {
                        $dictionaryTerm = new DictionaryTerm;
                        $dictionaryTerm->term = $term;
                        $dictionaryTerm->soundex = $soundex;
                        $dictionaryTerm->frequency = 0;
                        $dictionaryTerm->save();
                    }
                //  Add to our array of terms.
                    $dictionaryTerms[$term] = $dictionaryTerm;
                    $this->line($term);
                }
            //  This term has been used 1 more time than before.
                $dictionaryTerm->frequency++;
                $dictionaryTerm->save();
                $currentTermId = $dictionaryTerm->id;
                
                if ($previousTermId1) {
                //  Create or update the corresponding bigram for distance 1.
                    $bigramKey = $previousTermId1.'-'.$dictionaryTerm->id.'-1';
                    if (isset($dictionaryBigrams[$bigramKey])) {
                        $dictionaryBigram = $dictionaryBigrams[$bigramKey];
                    }
                    else {
                        $dictionaryBigram = new DictionaryBigram;
                        $dictionaryBigram->word_a_id = $previousTermId1;
                        $dictionaryBigram->word_b_id = $currentTermId;
                        $dictionaryBigram->distance = 1;
                        $dictionaryBigram->frequency = 0;
                    }
                    $dictionaryBigram->frequency++;
                    $dictionaryBigram->save();
                    $dictionaryBigrams[$bigramKey] = $dictionaryBigram;
                    
                    if ($previousTermId2) {
                    //  Create or update the corresponding bigram for distance 2.
                        $bigramKey = $previousTermId2.'-'.$dictionaryTerm->id.'-2';
                        if (isset($dictionaryBigrams[$bigramKey])) {
                            $dictionaryBigram = $dictionaryBigrams[$bigramKey];
                        }
                        else {
                            $dictionaryBigram = new DictionaryBigram;
                            $dictionaryBigram->word_a_id = $previousTermId2;
                            $dictionaryBigram->word_b_id = $currentTermId;
                            $dictionaryBigram->distance = 2;
                            $dictionaryBigram->frequency = 0;
                        }
                        $dictionaryBigram->frequency++;
                        $dictionaryBigram->save();
                        $dictionaryBigrams[$bigramKey] = $dictionaryBigram;
                    }
                   
                }
            //  This is now the "previous term", that we'll use to create a bigram.
                $previousTermId2 = $previousTermId1;
                $previousTermId1 = $currentTermId;
                
            }   //  foreach termsRaw

        }   //  foreach conditionsRaw
        
        return;
        
        $this->info('Done.');
        $this->line('Exiting...');
    }
    
    
    
    
}	//	class Vernacular
