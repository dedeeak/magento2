<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Framework\Backup\Db;

interface BackupInterface
{
    /**
     * Set backup time
     *
     * @param int $time
     * @return $this
     */
    public function setTime($time);

    /**
     * Set backup type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type);

    /**
     * Set backup path
     *
     * @param string $path
     * @return $this
     */
    public function setPath($path);

    /**
     * Set backup name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Open backup file (write or read mode)
     *
     * @param bool $write
     * @return $this
     */
    public function open($write = false);

    /**
     * Write to backup file
     *
     * @param string $data
     * @return $this
     */
    public function write($data);

    /**
     * Close open backup file
     *
     * @return $this
     */
    public function close();
}
