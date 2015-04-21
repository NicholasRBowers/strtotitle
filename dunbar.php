<?php

function titleCase($string) {
  // Remove no_parse content.
    $string_array = preg_split("/(<no_parse>|<\/no_parse>)+/i", $string);
    $newString = '';
    for ($k=0; $k < count($string_array); $k = $k + 2) {
      $string = $string_array[$k];
      // If the entire string is upper case, don't perform any title case on it.
        if ($string != strtoupper($string)) {

            /*
                TITLE CASE RULES:
                1. Uppercase the first character in every word.
            */
            $new = preg_replace("/(^|\s|\'|'|\"|-){1}([a-z]){1}/ie", "''.stripslashes('\\1').''.stripslashes(strtoupper('\\2')).''", $string);

            /*
                2. Lower case words exempt from title case.
                    a. Lowercase all articles, coordinate conjunctions ("and", "or", "nor"), and prepositions regardless of length, when they are other than the first or last word.
                    b. Lowercase the "to" in an infinitive." - this rule is of course approximated since it is context sensitive.
            */
            $matches = array();

            preg_match_all("/(\sof|\sa|\san|\sthe|\sbut|\sor|\snot|\syet|\sat|\son|\sin|\sover|\sabove|\sunder|\sbelow|\sbehind|\snext\sto|\sbeside|\sby|\samong|\sbetween|\sby|\still|\ssince|\sduring|\sfor|\sthroughout|\sto|\sand){2}/i", $new, $matches);

            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    $new = preg_replace("/(".$matches[$i][$j]."\s)/ise", "''.strtolower('\\1').''", $new);
                }
            }

            // 3.) Do not allow upper case apostrophes.
              $new = preg_replace("/(\w'S)/ie","''.strtolower('\\1').''",$new);
              $new = preg_replace("/(\w'\w)/ie","''.strtolower('\\1').''",$new);
              $new = preg_replace("/(\W)(of|a|an|the|but|or|not|yet|at|on|in|over|above|under|below|behind|next to| beside|by|amoung|between|by|till|since|durring|for|throughout|to|and)(\W)/ise","'\\1'.strtolower('\\2').'\\3'",$new);
            // 4.) Capitalize first letter in the string always.
              $new = preg_replace("/(^[a-z]){1}/ie","''.strtoupper('\\1').''", $new);
            // 5.) Replace special cases.
              // You will add to this as you find case specific problems.
              $new = preg_replace("/\sin-/i"," In-",$new);
              $new = preg_replace("/(\s|\"|\'){1}(ph){1}(\s|,|\.|\"|\'|:|!|\?|\*|$){1}/ie","'\\1pH\\3'",$new);
              $new = preg_replace("/^ph(\s|$)/i","pH ",$new);
              $new = preg_replace("/(\s)ph($)/i"," pH",$new);
              $new = preg_replace("/(\s|\"|\'){1}(&){1}(\s|,|\.|\"|\'|:|!|\?|\*){1}/ie","'\\1and\\3'",$new);
              $new = preg_replace("/(\s|\"|\'){1}(groundwater){1}(\s|,|\.|\"|\'|:|!|\?|\*){1}/e","'\\1Ground Water\\3'",$new);
              $new = preg_replace("/(\W|^){1}(cross){1}(\s){1}(connection){1}(\W|$){1}/ie","'\\1\\2-\\4\\5'",$new); // Always hyphenate cross-connections.
              $new = preg_replace("/(\s|\"|\'){1}(vs\.){1}(\s|,|\.|\"|\'|:|!|\?|\*){1}/ie","'\\1Vs.\\3'",$new);
              $new = preg_replace("/(\s|\"|\'){1}(on-off){1}(\s|,|\.|\"|\'|:|!|\?|\*){1}/ie","'\\1On-Off\\3'",$new);
              $new = preg_replace("/(\s|\"|\'){1}(on-site){1}(\s|,|\.|\"|\'|:|!|\?|\*){1}/ie","'\\1On-Site\\3'",$new);
              // Special cases like Class A Fires.
              $new = preg_replace("/(\s|\"|\'){1}(class\s){1}(\w){1}(\s|,|\.|\"|\'|:|!|\?|\*|$){1}/ie","'\\1\\2'.strtoupper('\\3').'\\4'",$new);
              $new = stripslashes($new);
              $string_array[$k] = $new;
        }
    }
    for ($k=0; $k<count($string_array); $k++) {
      $newString .= $string_array[$k];
    }
    return($newString);
}