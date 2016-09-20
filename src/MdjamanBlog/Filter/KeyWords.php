<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2016 Marcel Djaman
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace MdjamanBlog\Filter;

use Zend\Filter\AbstractFilter;

/**
 * Description of KeyWords
 *
 * @author Marcel Djaman <marceldjaman@gmail.com>
 */
class KeyWords extends AbstractFilter
{

    protected $stopWords = ["alors", "au", "aucun", "aussi", "autre", "avant",
        "avec", "avoir", "bon", "car", "ce", "cela", "ces", "ceux", "chaque",
        "ci", "comme", "comment", "dans", "des", "du", "dedans", "dehors",
        "depuis", "deux", "devrait", "doit", "donc", "dos", "droite", "dÀ©but",
        "elle", "elles", "en", "encore", "essai", "est", "et", "eu", "faire", "fait",
        "faites", "fois", "font", "force", "haut", "hors", "ici", "il", "ils",
        "je", "juste", "la", "le", "les", "leur", "là ", "ma", "maintenant",
        "mais", "mes", "mine", "moins", "mon", "mot", "même", "ni", "nommés",
        "notre", "nous", "nouveaux", "ou", "où", "par", "parce", "parole", "pas",
        "personnes", "peut", "peu", "pièce", "plupart", "plus", "pour", "pourquoi",
        "quand", "que", "quel", "quelle", "quelles", "quels", "qui", "sa",
        "sans", "ses", "seulement", "si", "sien", "son", "sont", "sous", "soyez",
        "sujet", "sur", "ta", "tandis", "tellement", "tels", "tes", "ton", "toujours",
        "tous", "tout", "trop", "très", "tu", "une", "valeur", "voie", "voient", "vont",
"votre", "vous", "vu", "y", "ça", "étaient", "état", "étions", "été", "être"];

    /**
     * Find and return array of keywords from given string
     * @param string $string
     * @param int $count
     * @return array
     */
    public function filter($string, $count = 10)
    {

        $clean = $this->cleanUp($string);
        preg_match_all('/\b.*?\b/i', $clean, $matchWords);
        $matchWords = $matchWords[0];

        foreach ($matchWords as $key => $item) {
            if ($item == '' || in_array($item, $this->stopWords) || strlen($item) <= 3) {
                unset($matchWords[$key]);
            }
        }

        $wordCountArr = [];

        if (is_array($matchWords)) {
            foreach ($matchWords as $key => $val) {
                $val = strtolower($val);
                if (!isset($wordCountArr[$val]))
                    $wordCountArr[$val] = array();

                if (isset($wordCountArr[$val]['count']))
                    $wordCountArr[$val]['count'] ++;
                else
                    $wordCountArr[$val]['count'] = 1;
            }
        }

        arsort($wordCountArr);
        $wordCountArr = array_slice($wordCountArr, 0, $count);

        return $wordCountArr;
    }

    /**
     * get stopwords
     * 
     * @return the $stopWords
     */
    public function getStopWords()
    {
        return $this->stopWords;
    }

    /**
     * set stopwords
     * 
     * @param array $stopWords
     */
    public function setStopWords(array $stopWords)
    {
        $this->stopWords = $stopWords;
    }

    /**
     * @param $text
     * @return mixed|string
     */
    private function cleanUp($text)
    {
        $clean = htmlspecialchars_decode($text);
        $clean = preg_replace('/\s\s+/i', '', $text); // replace whitespace
        $clean = trim($clean);
        $clean = preg_replace('/[^a-zA-Z0-9 -]/', '', $clean); // only take alphanumerical characters, but keep the spaces and dashes tooâ€¦
        $clean = strtolower($clean);

        return $clean;
    }

}
