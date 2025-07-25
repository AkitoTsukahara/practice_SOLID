<?php

// An example of a code that does not conform to the Liskov substitution principle
// Referring to https://accesto.com/blog/solid-php-solid-principles-in-php#LiskovSubstitutionPrinciple

interface CalculableArea
{
    public function calculateArea(): int;
}
class Rectangle implements CalculableArea
{
    protected int $width;
    protected int $height;

    public function __construct(int $width = 0, int $height = 0)
    {
        $this->width = $width;
        $this->height = $height;
    }

    public function calculateArea(): int
    {
        return $this->width * $this->height;
    }
}

class Square implements CalculableArea
{
    protected int $edge;

    public function __construct(int $edge)
    {
        $this->edge = $edge;
    }

    public function calculateArea(): int
    {
        return $this->edge ** 2;
    }

}

class RectangleTest extends TestCase
{
    public function testCalculateArea()
    {
        $shape = new Rectangle(10, 2);
        $this->assertEquals($shape->calculateArea(), 20);

        $shape = new Rectangle(5, 2);
        $this->assertEquals($shape->calculateArea(), 10);
    }
}

class SquareTest extends TestCase
{
    public function testCalculateArea()
    {
        $shape = new Square(10);
        $this->assertEquals($shape->calculateArea(), 100);

        $shape = new Square(5);
        $this->assertEquals($shape->calculateArea(), 25);

    }
}
