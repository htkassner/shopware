<?php
/**
 * Shopware 4.0
 * Copyright © 2012 shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 *
 * @category   Shopware
 * @package    Shopware_Models
 * @subpackage Article
 * @copyright  Copyright (c) 2012, shopware AG (http://www.shopware.de)
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

namespace Shopware\Models\Config;
use Shopware\Components\Model\ModelEntity,
    Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

/**
 * todo@all: Documentation
 *
 * @ORM\Table(name="s_core_config_forms")
 * @ORM\Entity
 */
class Form extends ModelEntity
{
    /**
     * @var integer $id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer $parentId
     * @ORM\Column(name="parent_id", type="integer", nullable=true)
     */
    private $parentId;

    /**
     * @var Form
     * @ORM\ManyToOne(targetEntity="Form", inversedBy="children", cascade={"all"})
     * @ORM\JoinColumn(name="parent_id", nullable=true, referencedColumnName="id", onDelete="SET NULL")
     */
    private $parent;

    /**
     * @var string $name
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    private $name;

    /**
     * @var string $label
     * @ORM\Column(name="label", type="string", nullable=true)
     */
    private $label;

    /**
     * @var string $description
     * @ORM\Column(name="description", type="string", nullable=false)
     */
    private $description;

    /**
     * @var integer $pluginId
     * @ORM\Column(name="plugin_id", type="integer", nullable=false)
     */
    private $pluginId;

    /**
     * @var string $position
     * @ORM\Column(name="position", type="integer", nullable=false)
     */
    private $position = 0;

    /**
     * @var Element[] $elements
     * @ORM\OneToMany(targetEntity="Element", mappedBy="form", cascade={"all"})
     * @ORM\OrderBy({"position" = "ASC", "id" = "ASC"})
     */
    private $elements;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="Form", mappedBy="parent", cascade={"all"}))
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $children;

    /**
     * @var \Shopware\Models\Plugin\Plugin
     * @ORM\ManyToOne(targetEntity="Shopware\Models\Plugin\Plugin", inversedBy="configForms")
     * @ORM\JoinColumn(name="plugin_id", referencedColumnName="id")
     */
    protected $plugin;

    /**
     * INVERSE SIDE
     * @ORM\OneToMany(targetEntity="Shopware\Models\Config\FormTranslation", mappedBy="form", orphanRemoval=true, cascade={"persist", "update"})
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $translations;


    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->elements = new ArrayCollection();
        $this->translations = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Form
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $label
     * @return Form
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return string
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $name
     * @return Element
     */
    public function getElement($name)
    {
        /** @var $value Element */
        foreach ($this->elements as $element) {
            if ($element->getName() === $name) {
                return $element;
            }
        }
        return null;
    }

    /**
     * @param string $type
     * @param string $name
     * @param array $options
     * @return Form
     */
    public function setElement($type, $name, $options = null)
    {
        /** @var $value Element */
        foreach ($this->elements as $element) {
            if ($element->getName() === $name) {
                $element->setType($type);
                if ($options !== null) {
                    $element->setOptions($options);
                }
                return $this;
            }
        }
        $this->addElement($type, $name, $options);
        return $this;
    }

    /**
     * @param Element|string $element
     * @param string $name
     * @param array $options
     * @return \Shopware\Models\Config\Form
     */
    public function addElement($element, $name = null, $options = null)
    {
        if (!$element instanceof Element) {
            $element = new Element(
                $element, $name, $options
            );
        }
        $element->setForm($this);
        $this->elements->add($element);
        return $this;
    }

    /**
     * @return ArrayCollection|Element[]
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * @return bool
     */
    public function hasElements()
    {
        return $this->elements->count() > 0;
    }

    /**
     * @deprecated
     * @return Form
     */
    public function save()
    {
        return $this;
    }

    /**
     * @param integer $pluginId
     */
    public function setPluginId($pluginId)
    {
        $this->pluginId = $pluginId;
    }

    /**
     * @return integer
     */
    public function getPluginId()
    {
        return $this->pluginId;
    }

    /**
     * @return Form
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Form $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return ArrayCollection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param ArrayCollection $children
     */
    public function setChildren($children)
    {
        $this->children = $children;
    }

    /**
     * @return Doctrine\Common\Collections\ArrayCollection
     */
    public function getTranslations()
    {
        return $this->translations;
    }


    /**
     * @param FormTranslation
     * @return \Shopware\Models\Config\Form
     */
    public function addTranslation($translation)
    {
        $this->translations->add($translation);
        $translation->setForm($this);
        return $this;
    }


    /**
     * @return bool
     */
    public function hasTranslations()
    {
        return $this->translations->count() > 0;
    }


}
