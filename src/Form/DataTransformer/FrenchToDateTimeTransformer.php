<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class FrenchToDateTimeTransformer implements DataTransformerInterface
{
    public function transform($date)
    {
        if ($date === null) {
            return '';
        }
        return $date->format('d/m/Y');
    }

    public function reverseTransform($franchDate)
    {
        // French date = 21/12/2019
        if ($franchDate === null) {
            // Exception
            throw new TransformationFailedException("Vous devez fournir une date");
            
        }

        $date = \DateTime::createFromFormat('d/m/Y', $franchDate);

        if ($date === false) {
            // Exception
            throw new TransformationFailedException("Le format de la date n'est pas bon");
        }

        return $date;
    }
}
