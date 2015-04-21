<?php

/* GOALS & NOTES:
    * BEST PRACTICE - Do away with nested ternary operators, they make everything unnecessarily complicated, and the PHP manual specifically states to avoid nesting them - "Note: It is recommended that you avoid "stacking" ternary expressions. PHP's behaviour when using more than one ternary operator within a single statement is non-obvious" (http://php.net/manual/en/language.operators.comparison.php).
    * BEST PRACTICE - Break up operation into more than one statement for ease of development.
*/ 

// Original Title Case script © John Gruber <daringfireball.net>
// JavaScript port © David Gouch <individed.com>
// PHP port of the above by Kroc Camen <camendesign.com>

function titleCase ($title) {
  // Remove HTML, storing it for later.
    // HTML elements to ignore    | tags  | entities
    $regx = '/<(code|var)[^>]*>.*?<\/\1>|<[^>]+>|&\S+;/';
    preg_match_all ($regx, $title, $html, PREG_OFFSET_CAPTURE);
    $title = preg_replace ($regx, '', $title);
  
  // Find each word (including punctuation attached).
    preg_match_all ('/[\w\p{L}&`\'‘’"“\.@:\/\{\(\[<>_]+-? */u', $title, $m1, PREG_OFFSET_CAPTURE);
    foreach ($m1[0] as &$m2) {
      // Shorthand these- "match" and "index".
        list ($m, $i) = $m2;
    
      // Correct offsets for multi-byte characters (`PREG_OFFSET_CAPTURE` returns *byte*-offset)
      // We fix this by recounting the text before the offset using multi-byte aware `strlen`
        $i = mb_strlen (substr ($title, 0, $i), 'UTF-8');
    
      // Find words that should always be lowercase.
        // Never on the first word, and never if preceded by a colon.
        $m = $i>0 && mb_substr ($title, max (0, $i-2), 1, 'UTF-8') !== ':' && !preg_match ('/[\x{2014}\x{2013}] ?/u', mb_substr ($title, max (0, $i-2), 2, 'UTF-8')) && preg_match ('/^(a(nd?|s|t)?|b(ut|y)|en|for|i[fn]|o[fnr]|t(he|o)|vs?\.?|via)[ \-]/i', $m)
        ? // And convert them to lowercase.
          mb_strtolower ($m, 'UTF-8') 
        : // Else: brackets and other wrappers.
          (preg_match ('/[\'"_{(\[‘“]/u', mb_substr ($title, max (0, $i-1), 3, 'UTF-8'))
            ? // Convert first letter within wrapper to uppercase.
              mb_substr ($m, 0, 1, 'UTF-8').
              mb_strtoupper (mb_substr ($m, 1, 1, 'UTF-8'), 'UTF-8').
              mb_substr ($m, 2, mb_strlen ($m, 'UTF-8')-2, 'UTF-8') 
            : // Else: do not uppercase these cases.
              (preg_match ('/[\])}]/', mb_substr ($title, max (0, $i-1), 3, 'UTF-8')) || preg_match ('/[A-Z]+|&|\w+[._]\w+/u', mb_substr ($m, 1, mb_strlen ($m, 'UTF-8')-1, 'UTF-8'))
                ? // If all else fails, then no more fringe-cases; uppercase the word.
                  $m
                :
                  mb_strtoupper (mb_substr ($m, 0, 1, 'UTF-8'), 'UTF-8').mb_substr ($m, 1, mb_strlen ($m, 'UTF-8'), 'UTF-8')
              )
          );
    
      // Re-splice the title with the change (`substr_replace` is not multi-byte aware).
        $title = mb_substr ($title, 0, $i, 'UTF-8').$m.mb_substr ($title, $i+mb_strlen ($m, 'UTF-8'), mb_strlen ($title, 'UTF-8'), 'UTF-8');
    }
  
  // Restore the HTML.
    foreach ($html[0] as &$tag) $title = substr_replace ($title, $tag[0], $tag[1], 0);
    return $title;
} ?>