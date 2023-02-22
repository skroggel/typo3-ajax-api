<?php
namespace Madj2k\AjaxApi\Tests\Unit\Helper;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Madj2k\AjaxApi\Helper\AjaxRequestHelper;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */


/**
 * AjaxRequestHelperTest
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_AjaxApi
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class AjaxRequestHelperTest extends UnitTestCase
{

    const FIXTURE_PATH = __DIR__ . '/AjaxRequestHelperTest/Fixtures';


    /**
     * @var \Madj2k\AjaxApi\Helper\AjaxRequestHelper|null
     */
    private ?AjaxRequestHelper $subject = null;


    /**
     * Setup
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(AjaxRequestHelper::class);

    }

    //=============================================

    /**
     * @test
     */
    public function initSetsContentUid ()
    {

        /**
         * Scenario:
         *
         * Given ajaxApi-array is set via $_GET
         * Given the contentUid is set in this array
         * When init is called
         * Then getContentUid returns the given value
         */
        $_GET['ajax_api']['cid'] = 95;
        $this->subject->initFromGetPost();
        self::assertEquals(95, $this->subject->getContentUid());
    }


    /**
     * @test
     */
    public function initSetsKey ()
    {

        /**
         * Scenario:
         *
         * Given ajaxApi-array is set via $_GET
         * Given the key is set in this array
         * When init is called
         * Then getKey returns the given value
         */
        $_GET['ajax_api']['key'] = 'beatMeBitch';
        $this->subject->initFromGetPost();
        self::assertEquals('beatMeBitch', $this->subject->getKey());
    }


    /**
     * @test
     */
    public function initSetsIdList ()
    {

        /**
         * Scenario:
         *
         * Given ajaxApi-array is set via $_GET
         * Given the idList is set in this array
         * When init is called
         * Then getIdList returns an array of the given ids
         */
        $_GET['ajax_api']['idl'] = '15,30';
        $this->subject->initFromGetPost();

        $result = $this->subject->getIdList();
        self::assertIsArray( $result );
        self::assertCount(2, $result );
        self::assertEquals(15, $result[0]);
        self::assertEquals(30, $result[1]);

    }

    //=============================================

    /**
     * @test
     */
    public function getIsAjaxCallReturnsFalseIfNotAjaxPageType ()
    {
        /**
         * Scenario:
         *
         * Given a pageType not equal to 250 is called
         * When isAjax is called
         * Then false is returned
         */
        $_GET['type'] = 0;
        self::assertFalse($this->subject->getIsAjaxCall());
    }


    /**
     * @test
     */
    public function getIsAjaxCallReturnsFalseIfAjaxParamsWithoutAjaxPageType ()
    {

        /**
         * Scenario:
         *
         * Given a pageType not equal to 250 is called
         * Given a ajax_api param is set
         * When isAjax is called
         * Then false is returned
         */
        $_GET['type'] = 0;
        $_GET['ajax_api'] = ['test'];
        self::assertFalse($this->subject->getIsAjaxCall());
    }


    /**
     * @test
     */
    public function getIsAjaxCallReturnsFalseIfAjaxPageTypeWithoutAjaxParams ()
    {
        /**
         * Scenario:
         *
         * Given a pageType equal to 250 is called
         * Given no ajax_api param is set
         * When isAjax is called
         * Then false is returned
         */
        $_GET['type'] = 250;
        self::assertFalse($this->subject->getIsAjaxCall());
    }


    /**
     * @test
     */
    public function getIsAjaxCallReturnsTrueIfAjaxPageTypeWithAjaxParams ()
    {
        /**
         * Scenario:
         *
         * Given a pageType equal to 250 is called
         * Given a ajax_api param is set
         * When isAjax is called
         * Then true is returned
         */
        $_GET['type'] = 250;
        $_GET['ajax_api'] = ['test'];
        self::assertTrue($this->subject->getIsAjaxCall());

    }

    //=============================================

    /**
     * @test
     */
    public function getKeyReturnsEmptyStringIfNothingSet ()
    {

        /**
         * Scenario:
         *
         * Given nothing is set
         * When getKey is called
         * Then empty is returned
         */
        self::assertEmpty($this->subject->getKey());
    }


    //=============================================

    /**
     * @test
     */
    public function getIdListReturnsValueSetWithSetIdList ()
    {
        /**
         * Scenario:
         *
         * Given a idList is set
         * When getIdList is called
         * Then the given idList is returned
         */
        $this->subject->setIdList([1,2,3,4,5]);
        self::assertEquals([1,2,3,4,5], $this->subject->getIdList());
    }


    /**
     * TearDown
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }

}
