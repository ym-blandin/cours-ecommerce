<?php

namespace App\Controller;

use Twig\Environment;
use App\Taxes\Calculator;
use Cocur\Slugify\Slugify;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController
{
    protected $calculator;

    public function __construct(Calculator $calculator)
    {
        $this->calculator = $calculator;
    }

    /**
     * @Route("/hello/{name?Wolrd}", name="hello")
     */
    public function hello($name, Slugify $slugify, Environment $twig)
    {
        dump($twig);

        dump($slugify->slugify("Hello World !"));

        $tva = $this->calculator->calcul(120);

        dump($tva);

        return new Response("Hello $name !");
    }
}
