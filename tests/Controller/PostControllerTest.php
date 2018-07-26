<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostControllerTest extends WebTestCase
{
    public function testShowPost()
    {
        $client = static::createClient();
        $client->request('GET', '/submit/form');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider submitDataProvider
     */
    public function testSubmitPost($data)
    {
        $input  = $data['input'];
        $output = $data['output'];

        $client  = static::createClient();
        $crawler = $client->request('POST', '/submit/form', $input);

        if ($output['isHtml']) {
            $result = $output['result'];
            $count  = $crawler->filter('html:contains("' . $result . '")')->count();

            $this->assertEquals(1, $count);
        } else {
            $this->assertEquals($output['result'], $client->getResponse()->getContent());
        }
    }

    public function submitDataProvider()
    {
        return [

            //test #1
            [
                [
                    'input'  => [
                        'name'        => 'Iphone10',
                        'description' => 'Very Good Good Good'
                    ],
                    'output' => [
                        'isHtml' => true,
                        'result' => 'Name at least 10 characters'
                    ]
                ]
            ],

            //test #2
            [
                [
                    'input'  => [
                        'name'        => 'Iphone10111223',
                        'description' => 'Very'
                    ],
                    'output' => [
                        'isHtml' => true,
                        'result' => 'Description at least 10 characters'
                    ]
                ]
            ],

            //test #3
            [
                [
                    'input'  => [
                        'name'        => 'Iphone10111223',
                        'description' => 'Very Godddoodddd'
                    ],
                    'output' => [
                        'isHtml' => false,
                        'result' => 'Saved successfully'
                    ]
                ]
            ],
        ];
    }
}