<?php

function errorPrint(){
        $exampleURL = "https://drive.google.com/open?id=1ViXGGxPqXLkkIZvjego42sXfSCbijG_e";
        printf("\n%s\n", str_repeat('#', 110));
        printf("## %s ##\n", str_repeat(" ", 104));
        printf("## %s %s %s ##\n", str_repeat(" ", 29), "Please Enter Your Google Drive Share URL !!!", str_repeat(" ", 29));
        printf("## Eaxmple: %s %s %s  ##\n", "php", basename(__FILE__), $exampleURL);
        printf("## %s ##\n", str_repeat(" ", 104));
        printf("%s\n\n", str_repeat('#', 110));
        return 0;
}

function foolProofing(array $argv){
        if(!isset($argv)){
                errorPrint();
                return 0;
        }

        $match = array();
        $pattern = "/https:\/\/drive\.google\.com\/open\?id=(.*)/";
        @preg_match($pattern, $argv[1], $match);

        if(!isset($match[1])){
                errorPrint();
                return 0;
        }
        return $match[1];
}

$judge = foolProofing($argv);

if(0 === $judge){
        return 0;
}else{
        $downloadID = $judge;
        $downloadURL = "https://drive.google.com/uc?export=download&id=" . $downloadID;
        $cmd = "curl -LOJ \"" . $downloadURL . "\"";
        $output = shell_exec($cmd);
        printf("%s\n", $output);
}
