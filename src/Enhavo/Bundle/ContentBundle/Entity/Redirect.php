<?php

namespace Enhavo\Bundle\ContentBundle\Entity;

use Enhavo\Bundle\AppBundle\Model\RouteInterface;
use Enhavo\Bundle\ContentBundle\Model\RedirectInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;

/**
 * Redirect
 */
class Redirect implements RedirectInterface, ResourceInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $from;

    /**
     * @var string
     */
    private $to;

    /**
     * @var integer
     */
    private $code;

    /**
     * @var \Enhavo\Bundle\AppBundle\Entity\Route
     */
    private $route;

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
     * Set from
     *
     * @param string $from
     * @return Redirect
     */
    public function setFrom($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Get from
     *
     * @return string 
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set to
     *
     * @param string $to
     * @return Redirect
     */
    public function setTo($to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * Get to
     *
     * @return string 
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Set route
     *
     * @param RouteObjectInterface $route
     * @return Redirect
     */
    public function setRoute(RouteObjectInterface $route = null)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get route
     *
     * @return RouteInterface
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param integer $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }
}
