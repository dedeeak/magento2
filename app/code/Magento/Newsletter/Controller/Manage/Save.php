<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Newsletter\Controller\Manage;

use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;
use Magento\Customer\Api\Data\CustomerDataBuilder;

class Save extends \Magento\Newsletter\Controller\Manage
{
    /**
     * @var \Magento\Core\App\Action\FormKeyValidator
     */
    protected $formKeyValidator;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var CustomerDataBuilder
     */
    protected $customerBuilder;

    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    protected $subscriberFactory;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Core\App\Action\FormKeyValidator $formKeyValidator
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param CustomerRepository $customerRepository
     * @param CustomerDataBuilder $customerBuilder
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Core\App\Action\FormKeyValidator $formKeyValidator,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        CustomerRepository $customerRepository,
        CustomerDataBuilder $customerBuilder,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
    ) {
        $this->storeManager = $storeManager;
        $this->formKeyValidator = $formKeyValidator;
        $this->customerRepository = $customerRepository;
        $this->customerBuilder = $customerBuilder;
        $this->subscriberFactory = $subscriberFactory;
        parent::__construct($context, $customerSession);
    }

    /**
     * Save newsletter subscription preference action
     *
     * @return void|null
     */
    public function execute()
    {
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $this->_redirect('customer/account/');
        }

        $customerId = $this->_customerSession->getCustomerId();
        if (is_null($customerId)) {
            $this->messageManager->addError(__('Something went wrong while saving your subscription.'));
        } else {
            try {
                $customer = $this->customerRepository->getById($customerId);
                $storeId = $this->storeManager->getStore()->getId();
                $updatedCustomer = $this->customerBuilder->populate($customer)->setStoreId($storeId)->create();
                $this->customerRepository->save($updatedCustomer);
                if ((boolean)$this->getRequest()->getParam('is_subscribed', false)) {
                    $this->subscriberFactory->create()->subscribeCustomerById($customerId);
                    $this->messageManager->addSuccess(__('We saved the subscription.'));
                } else {
                    $this->subscriberFactory->create()->unsubscribeCustomerById($customerId);
                    $this->messageManager->addSuccess(__('We removed the subscription.'));
                }
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Something went wrong while saving your subscription.'));
            }
        }
        $this->_redirect('customer/account/');
    }
}
