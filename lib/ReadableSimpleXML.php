<?php
/**
 * By mjijackson@gmail.com
 * http://php.tonnikala.org/manual/ru/function.simplexml-element-asXML.php
 *
 * Makes indentation without using DOMdocument, using a simple extend.
 */
class ReadableXMLElement extends SimpleXMLElement
{

    /**
     * Outputs this element as pretty XML to increase readability.
     *
     * @param   int     $level      (optional) The number of spaces to use for
     *                              indentation, defaults to 4
     * @return  string              The XML output
     * @access  public
     */
    public function asPrettyXML($level = 4)
    {
        // get an array containing each XML element
        $xml = explode("\n", preg_replace('/>\s*</', ">\n<", $this->asXML()));

        // hold current indentation level
        $indent = 0;

        // hold the XML segments
        $pretty = array();

        // shift off opening XML tag if present
        if (count($xml) && preg_match('/^<\?\s*xml/', $xml[0])) {
            $pretty[] = array_shift($xml);
        }

        foreach ($xml as $el) {
            if (preg_match('/^<([\w])+[^>\/]*>$/U', $el)) {
                // opening tag, increase indent
                $pretty[] = str_repeat(' ', $indent) . $el;
                $indent += $level;
            } else {
                if (preg_match('/^<\/.+>$/', $el)) {
                    // closing tag, decrease indent
                    $indent -= $level;
                }
                $pretty[] = str_repeat(' ', $indent) . $el;
            }
        }

        return implode("\n", $pretty);
    }

}

?>

