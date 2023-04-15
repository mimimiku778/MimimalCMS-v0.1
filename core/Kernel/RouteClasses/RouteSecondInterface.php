<?php

/**
 * MimimalCMS0.1 APIs
 * 
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */

namespace Shadow\Kernel\RouteClasses;

use Shadow\Kernel\ResponseInterface;

interface RouteSecondInterface
{
    /**
     * Adds a validator to the route validator array.
     * 
     * * **Example:** `Route::path('home/{pageId}')->match(fn (string $pageId): bool => ctype_digit($pageId));`
     * 
     * @param \Closure $validator     The validator to be added.
     * @param string   $requestMethod [optional] Specify the request method to validate.
     * 
     * @return static The instance of the Route class.
     */
    public function match(\Closure $callback, ?string $requestMethod = null): static;

    /**
     * Adds a string validator to the route validator array.
     * 
     * **Example:** `Route::path('search/{keyword}')->matchStr('keyword'));`
     * 
     * @param string            $parametaName  The key of a value. It can be accessed using dot notation.
     * @param string|null       $requestMethod [optional] Specify the request method to validate. If null, applies to all HTTP methods.
     * @param int|null          $maxLen        [optional] The maximum length of the string.
     * @param string|array|null $regex         [optional] Specifies a regular expression pattern that the input string must match.
     *                                         If an array of strings is provided instead, a regular expression pattern will be generated from the array elements.
     * @param bool|null         $emptyAble     [optional] If the string can be empty or not.
     * @param string|null       $e             [optional] An Exception name to be thrown if validation fails.
     * 
     * @return static                    The instance of the Route class, to allow for method chaining.
     * 
     * @throws LogicException            If an error occurred in preg_match.
     * 
     * @throws InvalidArgumentException  If the elements of the `$regex` array contain non-strings.
     * 
     * @throws ValidationException       If the input string is invalid (not a string), does not match the specified regex pattern,
     *                                   or is empty when not allowed.  
     *                                   * Error codes:  
     *                                   1001 - The input must be a string.  
     *                                   1002 - The input string does not match the specified regex pattern.  
     *                                   1003 - The input string contains only whitespace characters or an empty string.  
     *                                   1004 - The input string exceeds the maximum length limit of {maxLen} characters.  
     */
    public function matchStr(
        string $parametaName,
        ?string $requestMethod = null,
        ?int $maxLen = null,
        string|array|null $regex = null,
        ?bool $emptyAble = false
    ): static;

    /**
     *  **Example:** `Route::path('home/{pageId}')->matchNum('pageId', min:1));`
     * 
     * @param string      $parametaName  The key of a value. It can be accessed using dot notation.
     * @param string|null $requestMethod [optional] Specify the request method to validate. If null, applies to all HTTP methods.
     * @param int|null    $max           [optional] The maximum numeric value.
     * @param int|null    $min           [optional] The minimum numeric value.
     * @param int|null    $exactMatch    [optional] The numeric value for exact match.
     * @param string|null $e             [optional] An Exception name to be thrown if validation fails.
     * 
     * @return static                    The instance of the Route class, to allow for method chaining.
     * 
     * @throws ValidationException       If the input fails validation.
     *                                   * Error codes:  
     *                                   2001 - The input must be an integer or a string containing only digits.  
     *                                   2002 - The input does not match the expected value.  
     *                                   2003 - The input must be greater than or equal to [min].  
     *                                   2004 - The input must be less than or equal to [max].  
     */
    public function matchNum(
        string $parametaName,
        ?string $requestMethod = null,
        ?int $max = null,
        ?int $min = null,
        ?int $exactMatch = null
    ): static;

    /**
     * Validates a file uploaded via HTTP request based on various criteria.
     *
     * @param string      $parametaName     The name of the file parameter in the HTTP request.
     * @param array       $allowedMimeTypes Array of allowed mime types for the file.
     *                                      * **Example:** `['image/jpeg', 'image/png', 'image/gif', 'image/webp']`
     * 
     * @param int         $maxFileSize      Maximum allowed file size in kilobytes (KB).
     * @param bool|null   $emptyAble        Whether an empty file is allowed or not. Defaults to true.
     * @param string|null $requestMethod    The HTTP request method. If null, applies to all HTTP methods.
     *
     * @return static  The instance of the class.
     *
     * @throws LogicException      If the file is not uploaded via HTTP POST.
     * 
     * @throws ValidationException If the file is too large, has an extension not allowed,
     *                             or has a mime type that does not match the file type.  
     *                             * Error codes:  
     *                             3001 - File too large.  
     *                             3002 - File extension not allowed.  
     *                             3003 - File type does not match.  
     */
    public function matchFile(
        string $parametaName,
        array $allowedMimeTypes,
        int $maxFileSize = DEFAULT_MAX_FILE_SIZE,
        ?bool $emptyAble = true,
        ?string $requestMethod = null,
    ): static;

    /**
     * Adds the specified middleware to the route.
     *
     * @param array       $name          The names of the middleware to add.
     * @param string|null $requestMethod The HTTP request method for which the middleware should be applied. 
     *                                   If null, the middleware will be applied to all request methods.
     *
     * @return static                    This Route instance, to allow for method chaining.
     */
    public function middleware(array $name, ?string $requestMethod = null): static;

    /**
     * Defines what to do when validation fails.
     * Captures `ValidationException` and saves the errors to the session's "Errors".
     * Takes a callback function as its argument, which is called when a redirect is required.
     * Only the `\redirect()` helper function can be passed as an argument.
     * 
     * @param ResponseInterface $redirect The callback function to call when a redirect is required.
     * @param string|null       $requestMethod The HTTP request method. If null, applies to all HTTP methods.
     * 
     * @return static The Route instance, to allow for method chaining.
     */
    public function fails(ResponseInterface $redirect, ?string $requestMethod = null): static;
}