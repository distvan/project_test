<?php

namespace Distvan;

use AutoDb\AutoDb;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Exception;

/**
 * Class Controller
 * Base controller class of the test application
 *
 * @package Distvan
 * @author Istvan Dobrentei
 * @link https://www.dobrenteiistvan.hu
 */
final class Controller
{
    const POPULATE_AUTHOR_NUMBER                = 500;
    const POPULATE_BOOK_NUMBER                  = 500;
    const POPULATE_CATEGORY_NUMBER              = 10;
    const POPULATE_MAX_AUTHOR_PER_BOOK_NUMBER   = 3;
    const RESULT_PER_PAGE                       = 30;
    const CATEGORY_TABLE                        = 'category';
    const AUTHOR_TABLE                          = 'author';
    const BOOK_TABLE                            = 'book';
    const BOOK_CATEGORY_TABLE                   = 'book_category';
    const BOOK_AUTHOR_TABLE                     = 'book_author';

    private $_db;
    private $_autoDb;
    private $_authors;
    private $_view;

    /**
     * Controller constructor.
     *
     * @param $db
     * @param $view
     */
    public function __construct($db, $view)
    {
        $this->_db = $db;
        $this->_autoDb = AutoDb::init($db);
        $this->_view = $view;
    }

    /**
     * Populate database with test data
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     */
    public function populateDb(Request $request, Response $response, $args)
    {
        ##################################################### first create 10 category

        $categories = array();
        for($i=1;$i<=self::POPULATE_CATEGORY_NUMBER;$i++)
        {
            try
            {
                $rec = $this->_autoDb->newRow(self::CATEGORY_TABLE);
                $rec->attr('name', 'category-name-' . $i);
                if($i % 2 == 0)
                {
                    $rec->attr('is_adult', 1);
                }
                $rec->save();
                array_push($categories, $rec->getPrimaryKeyValue());
            }
            catch(Exception $e)
            {
                echo $e->getMessage();
            }
        }

        ##################################################### and then add 500 authors

        $this->_authors = array();
        for($i=1;$i<=self::POPULATE_AUTHOR_NUMBER;$i++)
        {
            try
            {
                $rec = $this->_autoDb->newRow(self::AUTHOR_TABLE);
                $rec->attr('name', 'author-name-' . $i);
                $rec->save();
                array_push($this->_authors, $rec->getPrimaryKeyValue());
            }
            catch(Exception $e)
            {
                echo $e->getMessage();
            }
        }

        ##################################################### then add 500 books

        $books = array();
        for($i=1;$i<=self::POPULATE_BOOK_NUMBER;$i++)
        {
            try
            {
                $rec = $this->_autoDb->newRow(self::BOOK_TABLE);
                $rec->attr('title', 'book-title-' . $i);
                $rec->save();
                array_push($books, $rec->getPrimaryKeyValue());
            }
            catch(Exception $e)
            {
                echo $e->getMessage();
            }
        }

        ##################################################### and add categories only one to each book and many authors
        foreach($books as $bookId)
        {
            try
            {
                $rec = $this->_autoDb->newRow(self::BOOK_CATEGORY_TABLE);
                $rec->attr('book_id', $bookId);
                $key = array_rand($categories);
                $rec->attr('category_id', $categories[$key]);
                $rec->save();
                $this->addAuthor($bookId);
            }
            catch(Exception $e)
            {
                echo $e->getMessage();
            }
        }
    }

    /**
     * Select random authors and add to the book
     *
     * @param $bookId
     */
    private function addAuthor($bookId)
    {
        //select random authors
        $authorNum = rand(0, self::POPULATE_MAX_AUTHOR_PER_BOOK_NUMBER);
        if(!$authorNum)
        {
            return;
        }
        
        $randomAuthors = array_rand($this->_authors, $authorNum);
        if(count($randomAuthors)==1 && is_numeric($randomAuthors))
        {
            $randomAuthors = array($randomAuthors);
        }

        foreach($randomAuthors as $key)
        {
            try
            {
                $rec = $this->_autoDb->newRow(self::BOOK_AUTHOR_TABLE);
                $rec->attr('book_id', $bookId);
                $rec->attr('author_id', $this->_authors[$key]);
                $rec->save();
            }
            catch(Exception $e)
            {
                echo $e->getMessage();
            }
        }
    }

    /**
     * Show the main page
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     */
    public function showTable(Request $request, Response $response, $args)
    {
        $response = $this->_view->render($response, 'main.html');

        return $response;
    }

    /**
     * Get parameters from request and filter result
     * and return json response
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     */
    public function getBooks(Request $request, Response $response, $args)
    {
        $back = array();
        $filterData['name'] = trim(htmlspecialchars($args['filter_name'], ENT_QUOTES, 'utf-8'));
        $filterData['is_adult'] = (int)$args['filter_only_adult'];
        $filterData['page'] = (int)$args['page'];

        $back['total'] = $this->filteredBooksCount($filterData);
        $back['result'] = $this->filterBooks($filterData);
        $back['per_page'] = self::RESULT_PER_PAGE;

        return $response->withStatus(200)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($back));
    }

    /**
     *  Create filter query
     *
     * @param bool $counter
     * @param $filterData
     * @return string
     */
    private function createQuery($counter=false, $filterData)
    {
        $filterAdult = '';
        $limit = ' LIMIT ?,?';

        $sql = "SELECT title, name, is_adult, (SELECT GROUP_CONCAT(name SEPARATOR ',') FROM book_author ba 
                LEFT JOIN author a ON (ba.author_id=a.id)
                WHERE ba.book_id = b.id) AS authors";

        if($counter)
        {
            $sql = "SELECT count(*) AS sum ";
            $limit = "";
        }

        if($filterData['is_adult'])
        {
            $filterAdult = " AND c.is_adult='1'";
        }

        $sql .= " FROM book b
                 LEFT JOIN book_category bc ON (b.id=bc.book_id)
                 LEFT JOIN category c ON (bc.category_id=c.id)
                 WHERE (title like ? OR (SELECT GROUP_CONCAT(name SEPARATOR ',') FROM book_author ba 
                 LEFT JOIN author a ON (ba.author_id=a.id) WHERE ba.book_id = b.id) like ? ) " . $filterAdult . $limit;

        return $sql;
    }

    /**
     * Filter books and return result
     *
     * @param $filterData
     * @return array
     */
    private function filterBooks($filterData)
    {
        $sql = $this->createQuery(false, $filterData);
        $stmt = $this->_db->prepare($sql);
        $limit = self::RESULT_PER_PAGE;
        $offset = isset($filterData['page']) && $filterData['page'] ?  $filterData['page'] * self::RESULT_PER_PAGE : 0;
        $name = "%{$filterData['name']}%";
        $stmt->bind_param("ssii", $name, $name, $offset, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get the filtered result count
     *
     * @param $filterData
     * @return int
     */
    private function filteredBooksCount($filterData)
    {
        $sql = $this->createQuery(true, $filterData);
        $stmt = $this->_db->prepare($sql);
        $name = "%{$filterData['name']}%";
        $stmt->bind_param("ss", $name, $name);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_row();
        $stmt->close();

        return !empty($row) ? current($row) : 0;
    }

}