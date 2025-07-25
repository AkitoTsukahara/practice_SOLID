<?php

// An example of a code that does not conform to the Liskov substitution principle
// Referring to https://accesto.com/blog/solid-php-solid-principles-in-php#LiskovSubstitutionPrinciple

class Rectangle
{
    protected int $width;
    protected int $height;

    public function setWidth(int $width): void {
        $this->width = $width;
    }

    public function setHeight(int $height): void {
        $this->height = $height;
    }

    public function calculateArea(): int
    {
        return $this->width * $this->height;
    }
}

class Square extends Rectangle
{
    public function setWidth(int $width): void
    {
        $this->width = $width;
        $this->height = $width;
    }

    public function setHeight(int $height): void
    {
        $this->width = $height;
        $this->height = $height;
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
