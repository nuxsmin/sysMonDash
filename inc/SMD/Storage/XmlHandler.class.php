<?php
/**
 * sysMonDash
 *
 * @author     nuxsmin
 * @link       https://github.com/nuxsmin/sysMonDash
 * @copyright  2012-2018 Rubén Domínguez nuxsmin@cygnux.org
 *
 * This file is part of sysMonDash.
 *
 * sysMonDash is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * sysMonDash is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with sysMonDash. If not, see <http://www.gnu.org/licenses/gpl-3.0-standalone.html>.
 */

namespace SMD\Storage;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMNodeList;
use ReflectionObject;

/**
 * Class XmlHandler para manejo básico de documentos XML
 * @package SMD\Storage
 */
class XmlHandler implements StorageInterface
{
    /**
     * @var mixed
     */
    protected $items;
    /**
     * @var string
     */
    protected $file;
    /**
     * @var DOMDocument
     */
    private $Dom;
    /**
     * @var DOMElement
     */
    private $root;

    /**
     * XmlHandler constructor.
     * @param $file
     */
    public function __construct($file)
    {
        $this->file = $file;
        $this->setDOM();
    }

    /**
     * Crear un nuevo documento XML
     */
    private function setDOM()
    {
        $this->Dom = new DOMDocument('1.0', 'utf-8');
    }

    /**
     * Cargar un archivo XML
     *
     * @param string $tag
     * @return $this
     * @throws \Exception
     */
    public function load($tag = 'root')
    {
        if (!$this->checkSourceFile()) {
            throw new \Exception(sprintf('No es posible leer/escribir el archivo: %s', $this->file));
        }

        $this->items = array();
        $this->Dom->load($this->file);

        $nodes = $this->Dom->getElementsByTagName($tag)->item(0)->childNodes;
        $this->items = $this->readChildNodes($nodes);

        return $this;
    }

    /**
     * Comprobar que el archivo existe y se puede leer/escribir
     * @return bool
     */
    protected function checkSourceFile()
    {
        return (is_writable($this->file) && filesize($this->file) > 0);
    }

    /**
     * Leer de forma recursiva los nodos hijos y devolver un array multidimensional
     *
     * @param DOMNodeList $NodeList
     * @return array
     */
    protected function readChildNodes(DOMNodeList $NodeList)
    {
        $nodes = array();

        foreach ($NodeList as $node) {
            /** @var $node DOMNode|DOMElement */
            if (is_object($node->childNodes) && $node->childNodes->length > 1) {
                if ($node->nodeName === 'item') {
                    $nodes[] = $this->readChildNodes($node->childNodes);
                } else {
                    $nodes[$node->nodeName] = $this->readChildNodes($node->childNodes);
                }
            } elseif ($node->nodeType === XML_ELEMENT_NODE) {
                $val = is_numeric($node->nodeValue) ? (int)$node->nodeValue : $node->nodeValue;

                if ($node->nodeName === 'item') {
                    $nodes[] = $val;
                } else {
                    if ($node->hasAttributes() && $node->getAttribute('type') === 'array') {
                        $nodes[$node->nodeName] = array();
                    } else {
                        $nodes[$node->nodeName] = $val;
                    }

                }
            }
        }

        return $nodes;
    }

    /**
     * Obtener un elemento del array
     *
     * @param $id
     * @return mixed
     */
    public function __get($id)
    {
        return isset($this->items[$id]) ? $this->items[$id] : null;
    }

    /**
     * Guardar el archivo XML
     *
     * @param string $tag
     * @return $this
     * @throws \Exception
     */
    public function save($tag = 'root')
    {
        if (null === $this->items) {
            throw new \Exception('No hay elementos para guardar');
        }

        $this->Dom->formatOutput = true;

        $this->root = $this->Dom->createElement($tag);
        $this->Dom->appendChild($this->root);
        $this->writeChildNodes($this->items, $this->root);
        $this->Dom->save($this->file);

        return $this;
    }

    /**
     * Crear los nodos hijos recursivamente a partir de un array multidimensional
     *
     * @param mixed $items
     * @param DOMNode $Node
     * @param null $type
     */
    protected function writeChildNodes($items, DOMNode $Node, $type = null)
    {
        foreach ($this->analyzeItems($items) as $key => $value) {
            if (is_int($key)) {
                $newNode = $this->Dom->createElement('item');
                $newNode->setAttribute('type', $type);
            } else {
                $newNode = $this->Dom->createElement($key);
            }

            if (is_array($value)) {
                $newNode->setAttribute('type', 'array');
                if (count($value) > 0) {
                    $this->writeChildNodes($value, $newNode, $key);
                }
            } elseif (is_object($value)) {
                $newNode->setAttribute('class', get_class($value));
                $newNode->appendChild($this->Dom->createTextNode(base64_encode(serialize($value))));
            } else {
                $newNode->appendChild($this->Dom->createTextNode(trim($value)));
            }

            $Node->appendChild($newNode);
        }
    }

    /**
     * Analizar el tipo de elementos
     *
     * @param mixed $items
     * @param bool $serialize
     * @return array
     */
    protected function analyzeItems($items, $serialize = false)
    {
        if (is_array($items)) {
            ksort($items);

            return $items;
        }

        if (is_object($items)) {
            return $serialize ? serialize($items) : $this->analyzeObject($items);
        }

        return array();

    }

    /**
     * Analizar un elemento del tipo objeto
     *
     * @param $object
     * @return array
     */
    protected function analyzeObject($object)
    {
        $items = array();
        $Reflection = new ReflectionObject($object);

        foreach ($Reflection->getProperties() as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($object);

            if (is_numeric($value) || is_bool($value)) {
                $items[$property->getName()] = (int)$value;
            } else {
                $items[$property->getName()] = $value;
            }

            $property->setAccessible(false);
        }

        ksort($items);

        return $items;
    }

    /**
     * Devolver los elementos cargados
     *
     * @return mixed
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Establecer los elementos
     *
     * @param $items
     * @return mixed
     */
    public function setItems($items)
    {
        $this->items = $items;
    }
}