<?php

class SVG {

    var $CI;

    function SVG() {

        $this->CI = & get_instance();
    }

    function parseSVG($obj, $layers) {

  
        $buf = array();
        $idPath = array();
        $cont = 1;
        $ahandle = 0;
        $atype = null;
        $alayer = "";
        if (!is_array($layers)) {
            $layers = array();
        }

        foreach ($obj as $k => $el) {
//			print_r($k);
//                        echo '-------------------el--------------------';
//                        print_r($el);
            if ($k == "CADConverterDwgEntity") {
                
                $ahandle = $el->Handle;
                $atype = $el->Type;
                $alayer = $el->Layer;
                settype($alayer, "string");
                if(in_array($ahandle, $idPath)){
                      $ahandle.='_'.$cont;
                      $cont++;
                }else{
                    $idPath = implode(",", $idPath);
                    $idPath.=','.$ahandle;
                    $idPath = explode(",", $idPath);
                   
                }
               
            } elseif ($k == "path") {
                
                if (is_numeric(array_search($alayer, $layers)) && $atype != "AcDbText") {
                    $buf[$alayer][] = "<path  id='" . $ahandle . "' d='" . $el['d'] . "' stroke='" . $el['stroke'] . "' stroke-width='" . $el['stroke-width'] . "' " .
                            "fill='" . $el['fill'] . "'></path>\n";
                } else {
                    $buf[$alayer][] = "<path  d='" . $el['d'] . "' stroke='" . $el['stroke'] . "' stroke-width='" . $el['stroke-width'] . "' " .
                            "fill='" . $el['fill'] . "'/>\n";
                }
            }
        }
        

        $buffer = array();

        $buffer[] = " <g id='originalSVG'>\n";
        foreach ($buf as $layer => $section) {
            $buffer[] = "<g id='" . $layer . "'>\n" . implode("  ", $section) . "</g>\n";
        }
        $buffer[] = " </g>";
        return implode("", $buffer);
    }

    function getLayers($obj) {

        $buf = array();
        foreach ($obj as $k => $el) {
            if ($k == "CADConverterDwgEntity" && $el->Layer != "") {
                $buf[] = (string) $el->Layer;
            }
        }

        $buf2 = array_unique($buf);
        return $buf2;
    }

}
