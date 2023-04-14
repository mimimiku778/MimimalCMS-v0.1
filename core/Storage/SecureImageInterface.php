<?php

namespace Shadow\Storage;

interface SecureImageInterface
{
    /**
     * Returns a resized GD image resource based on the provided file path and maximum width/height constraints.
     *
     * @param string $filePath The path of the image file.
     * @param int|null $maxWidth The maximum width constraint. Defaults to the original width if not provided.
     * @param int|null $maxHeight The maximum height constraint. Defaults to the original height if not provided.
     *
     * @return GdImage A GD image resource of the resized image.
     *
     * @throws RuntimeException If an error occurs during the operation
     *                            with an error code as the second argument.
     *                            * Error codes:  
     *                            5000 - Invalid File.  
     *                            5001 - Unable to create image from the given data.  
     *                            5002 - Unable to get size of the given image data.  
     *                            5003 - Failed to create a new image.  
     *                            5004 - Failed to resize the image.
     */
    public function getImage(string $filePath, ?int $maxWidth = null, ?int $maxHeight = null): \GdImage;
}
