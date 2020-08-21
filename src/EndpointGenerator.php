<?php

namespace Anteris\Autotask\Generator;

use Anteris\Autotask\Generator\DataTransferObject\EndpointDataTransferObject;
use Anteris\Autotask\Generator\DataTransferObject\EntityInformationDataTransferObject;
use Anteris\Autotask\Generator\Helper\File;
use Anteris\Autotask\Generator\Helper\Str;
use Exception;
use GuzzleHttp\Client;
use Twig\Environment;

class EndpointGenerator extends AbstractFileWriter
{
    /** @var Client An HTTP client for getting information from Autotask. */
    protected Client $client;

    /** @var EndpointDataTransferObject Stores a singular and plural combo form of the endpoint name.  */
    protected EndpointDataTransferObject $endpoint;

    /** @var array Contains all the field definitions for the Data Transfer Object. */
    protected array $dtoFields    = [];

    /** @var array Contains all the imports required for the Data Transfer Object. */
    protected array $dtoImports   = [];

    /** @var array Contains all the imports required for the Service Object */
    protected $serviceImports = [
        'Spatie\\DataTransferObject\\DataTransferObject',
    ];

    /**
     * @var  array This lengthy and annoying array helps us define correct create / update routes for pesky nested resources.
     * @todo Make this less awkward in the code, maybe automatically generated later on.
     */
    protected array $weirdEndpoints = [
        'ChecklistLibraryChecklistItems' => [
            'parentPath' => 'ChecklistLibraries/{{ ref | raw }}/ChecklistItems',
            'parentRef'  => 'checklistLibraryID',
        ],
        'CompanyAlerts' => [
            'parentPath' => 'Companies/{{ ref | raw }}/Alerts',
            'parentRef'  => 'companyID',
        ],
        'CompanyAttachments' => [
            'parentPath' => 'Companies/{{ ref | raw }}/Attachments',
            'parentRef'  => 'parentID',
        ],
        'Contacts' => [
            'parentPath' => 'Companies/{{ ref | raw }}/Contacts',
            'parentRef'  => 'companyID',
        ],
        'CompanyLocations' => [
            'parentPath' => 'Companies/{{ ref | raw }}/Locations',
            'parentRef'  => 'companyID',
        ],
        'CompanyNotes' => [
            'parentPath' => 'Companies/{{ ref | raw }}/Notes',
            'parentRef'  => 'companyID',
        ],
        'CompanySiteConfigurations' => [
            'parentPath' => 'Companies/{{ ref | raw }}/SiteConfigurations',
            'parentRef'  => 'companyID',
        ],
        'CompanyTeams' => [
            'parentPath' => 'Companies/{{ ref | raw }}/Teams',
            'parentRef'  => 'companyID',
        ],
        'CompanyToDos' => [
            'parentPath' => 'Companies/{{ ref | raw }}/ToDos',
            'parentRef'  => 'companyID',
        ],
        'CompanyWebhookExcludedResources' => [
            'parentPath' => 'CompanyWebhooks/{{ ref | raw }}/ExcludedResources',
            'parentRef'  => 'resourceID',
        ],
        'CompanyWebhookFields' => [
            'parentPath' => 'CompanyWebhooks/{{ ref | raw }}/Fields',
            'parentRef'  => 'webhookID',
        ],
        'CompanyWebhookUdfFields' => [
            'parentPath' => 'CompanyWebhooks/{{ ref | raw }}/UdfFields',
            'parentRef'  => 'webhookID',
        ],
        'ConfigurationItemBillingProductAssociations' => [
            'parentPath' => 'ConfigurationItems/{{ ref | raw }}/BillingProductAssociations',
            'parentRef'  => 'configurationItemID',
        ],
        'ConfigurationItemCategoryUdfAssociations' => [
            'parentPath' => 'ConfigurationItemCategories/{{ ref | raw }}/UdfAssociations',
            'parentRef'  => 'configurationItemCategoryID',
        ],
        'ConfigurationItemNotes' => [
            'parentPath' => 'ConfigurationItems/{{ ref | raw }}/Notes',
            'parentRef'  => 'configurationItemID',
        ],
        'ContactBillingProductAssociations' => [
            'parentPath' => 'Contacts/{{ ref | raw }}/BillingProductAssociations',
            'parentRef'  => 'contactID',
        ],
        'ContactGroupContacts' => [
            'parentPath' => 'ContactGroups/{{ ref | raw }}/Contacts',
            'parentRef'  => 'contactGroupID',
        ],
        'ContactWebhookExcludedResources' => [
            'parentPath' => 'ContactWebhooks/{{ ref | raw }}/ExcludedResources',
            'parentRef'  => 'resourceID',
        ],
        'ContactWebhookFields' => [
            'parentPath' => 'ContactWebhooks/{{ ref | raw }}/Fields',
            'parentRef'  => 'webhookID',
        ],
        'ContactWebhookUdfFields' => [
            'parentPath' => 'ContactWebhooks/{{ ref | raw }}/UdfFields',
            'parentRef'  => 'webhookID',
        ],
        'ContractBillingRules' => [
            'parentPath' => 'Contracts/{{ ref | raw }}/BillingRules',
            'parentRef'  => 'contractID',
        ],
        'ContractBlockHourFactors' => [
            'parentPath' => 'Contracts/{{ ref | raw }}/BlockHourFactors',
            'parentRef'  => 'contractID',
        ],
        'ContractBlocks' => [
            'parentPath' => 'Contracts/{{ ref | raw }}/Blocks',
            'parentRef'  => 'contractID',
        ],
        'ContractCharges' => [
            'parentPath' => 'Contracts/{{ ref | raw }}/Charges',
            'parentRef'  => 'contractID',
        ],
        'ContractExclusionBillingCodes' => [
            'parentPath' => 'Contracts/{{ ref | raw }}/ExclusionBillingCodes',
            'parentRef'  => 'contractID',
        ],
        'ContractExclusionRoles' => [
            'parentPath' => 'Contracts/{{ ref | raw }}/ExclusionRoles',
            'parentRef'  => 'contractID',
        ],
        'ContractExclusionSetExcludedRoles' => [
            'parentPath' => 'ContractExlusionSets/{{ ref | raw }}/ExcludedRoles',
            'parentRef'  => 'contractExlusionSetID',
        ],
        'ContractExclusionSetExcludedWorkTypes' => [
            'parentPath' => 'ContractExlusionSets/{{ ref | raw }}/ExcludedWorkTypes',
            'parentRef'  => 'contractExlusionSetID',
        ],
        'ContractMilestones' => [
            'parentPath' => 'Contracts/{{ ref | raw }}/Milestones',
            'parentRef'  => 'contractID',
        ],
        'ContractNotes' => [
            'parentPath' => 'Contracts/{{ ref | raw }}/Notes',
            'parentRef'  => 'contractID',
        ],
        'ContractRates' => [
            'parentPath' => 'Contracts/{{ ref | raw }}/Rates',
            'parentRef'  => 'contractID',
        ],
        'ContractRetainers' => [
            'parentPath' => 'Contracts/{{ ref | raw }}/Retainers',
            'parentRef'  => 'contractID',
        ],
        'ContractRoleCosts' => [
            'parentPath' => 'Contracts/{{ ref | raw }}/RoleCosts',
            'parentRef'  => 'contractID',
        ],
        'ContractServiceAdjustments' => [
            'parentPath' => 'Contracts/{{ ref | raw }}/ServiceAdjustments',
            'parentRef'  => 'contractID',
        ],
        'ContractServiceBundleAdjustments' => [
            'parentPath' => 'Contracts/{{ ref | raw }}/ServiceBundleAdjustments',
            'parentRef'  => 'contractID',
        ],
        'ContractServiceBundles' => [
            'parentPath' => 'Contracts/{{ ref | raw }}/ServiceBundles',
            'parentRef'  => 'contractID',
        ],
        'ContractServiceBundleUnits' => [
            'parentPath' => 'Contracts/{{ ref | raw }}/ServiceBundleUnits',
            'parentRef'  => 'contractID',
        ],
        'ContractServices' => [
            'parentPath' => 'Contracts/{{ ref | raw }}/Services',
            'parentRef'  => 'contractID',
        ],
        'ContractServiceUnits' => [
            'parentPath' => 'Contracts/{{ ref | raw }}/ServiceUnits',
            'parentRef'  => 'contractID',
        ],
        'ContractTicketPurchases' => [
            'parentPath' => 'Contracts/{{ ref | raw }}/TicketPurchases',
            'parentRef'  => 'contractID',
        ],
        'ExpenseItems' => [
            'parentPath' => 'Expenses/{{ ref | raw }}/Items',
            'parentRef'  => 'expenseReportID',
        ],
        'Holidays' => [
            'parentPath' => 'HolidaySets/{{ ref | raw }}/Holidays',
            'parentRef'  => 'holidaySetID',
        ],
        'InventoryItemSerialNumbers' => [
            'parentPath' => 'InventoryItems/{{ ref | raw }}/SerialNumbers',
            'parentRef'  => 'inventoryItemID',
        ],
        'OpportunityAttachments' => [
            'parentPath' => 'Opportunities/{{ ref | raw }}/Attachments',
            'parentRef'  => 'opportunityID',
        ],
        'OrganizationalResources' => [
            'parentPath' => 'OrganizationalLevelAssociations/{{ ref | raw }}/Resources',
            'parentRef'  => 'organizationalLevelAssociationID',
        ],
        'Phases' => [
            'parentPath' => 'Projects/{{ ref | raw }}/Phases',
            'parentRef'  => 'projectID',
        ],
        'ProductNotes' => [
            'parentPath' => 'Products/{{ ref | raw }}/Notes',
            'parentRef'  => 'productID',
        ],
        'ProductTiers' => [
            'parentPath' => 'Products/{{ ref | raw }}/Tiers',
            'parentRef'  => 'productID',
        ],
        'ProductVendors' => [
            'parentPath' => 'Products/{{ ref | raw }}/Vendors',
            'parentRef'  => 'productID',
        ],
        'ProjectAttachments' => [
            'parentPath' => 'Projects/{{ ref | raw }}/Attachments',
            'parentRef'  => 'parentID',
        ],
        'ProjectCharges' => [
            'parentPath' => 'Projects/{{ ref | raw }}/Charges',
            'parentRef'  => 'projectID',
        ],
        'ProjectNotes' => [
            'parentPath' => 'Projects/{{ ref | raw }}/Notes',
            'parentRef'  => 'projectID',
        ],
        'PurchaseOrderItemReceiving' => [
            'parentPath' => 'PurchaseOrderItems/{{ ref | raw }}/Receiving',
            'parentRef'  => 'purchaseOrderItemID',
        ],
        'PurchaseOrderItems' => [
            'parentPath' => 'PurchaseOrders/{{ ref | raw }}/Items',
            'parentRef'  => 'orderID',
        ],
        'QuoteItems' => [
            'parentPath' => 'Quotes/{{ ref | raw }}/Items',
            'parentRef'  => 'quoteID',
        ],
        'ResourceRoleDepartments' => [
            'parentPath' => 'Resources/{{ ref | raw }}/RoleDepartments',
            'parentRef'  => 'resourceID',
        ],
        'ResourceRoleQueues' => [
            'parentPath' => 'Resources/{{ ref | raw }}/RoleQueues',
            'parentRef'  => 'resourceID',
        ],
        'ResourceRoles' => [
            'parentPath' => 'Resources/{{ ref | raw }}/Roles',
            'parentRef'  => 'resourceID',
        ],
        'ResourceServiceDeskRoles' => [
            'parentPath' => 'Resources/{{ ref | raw }}/ServiceDeskRoles',
            'parentRef'  => 'resourceID',
        ],
        'ResourceSkills' => [
            'parentPath' => 'Resources/{{ ref | raw }}/Skills',
            'parentRef'  => 'resourceID',
        ],
        'SalesOrder' => [
            'parentPath' => 'Opportunities/{{ ref | raw }}/SalesOrders',
            'parentRef'  => 'opportunityID',
        ],
        'ServiceBundleServices' => [
            'parentPath' => 'ServiceBundles/{{ ref | raw }}/Services',
            'parentRef'  => 'serviceBundleID',
        ],
        'ServiceCallTaskResources' => [
            'parentPath' => 'ServiceCallTasks/{{ ref | raw }}/Resources',
            'parentRef'  => 'serviceCallTaskID',
        ],
        'ServiceCallTasks' => [
            'parentPath' => 'ServiceCalls/{{ ref | raw }}/Tasks',
            'parentRef'  => 'serviceCallID',
        ],
        'ServiceCallTicketResources' => [
            'parentPath' => 'ServiceCallTickets/{{ ref | raw }}/Resources',
            'parentRef'  => 'serviceCallTicketID',
        ],
        'ServiceCallTickets' => [
            'parentPath' => 'ServiceCalls/{{ ref | raw }}/Tickets',
            'parentRef'  => 'serviceCallID',
        ],
        'ServiceLevelAgreementResults' => [
            'parentPath' => 'ServiceLevelAgreements/{{ ref | raw }}/Results',
            'parentRef'  => 'serviceCallTicketID',
        ],
        'SubscriptionPeriods' => [
            'parentPath' => 'Subscriptions/{{ ref | raw }}/Periods',
            'parentRef'  => 'subscriptionID',
        ],
        'TaskAttachments' => [
            'parentPath' => 'Tasks/{{ ref | raw }}/Attachments',
            'parentRef'  => 'parentID',
        ],
        'TaskNotes' => [
            'parentPath' => 'Tasks/{{ ref | raw }}/Notes',
            'parentRef'  => 'taskID',
        ],
        'TaskPredecessors' => [
            'parentPath' => 'Tasks/{{ ref | raw }}/Predecessors',
            'parentRef'  => 'taskID',
        ],
        'Tasks' => [
            'parentPath' => 'Projects/{{ ref | raw }}/Tasks',
            'parentRef'  => 'projectID',
        ],
        'TaskSecondaryResources' => [
            'parentPath' => 'Tasks/{{ ref | raw }}/SecondaryResources',
            'parentRef'  => 'taskID',
        ],
        'TicketAdditionalConfigurationItems' => [
            'parentPath' => 'Tickets/{{ ref | raw }}/AdditionalConfigurationItems',
            'parentRef'  => 'ticketID',
        ],
        'TicketAdditionalContacts' => [
            'parentPath' => 'Tickets/{{ ref | raw }}/AdditionalContacts',
            'parentRef'  => 'ticketID',
        ],
        'TicketAttachments' => [
            'parentPath' => 'Tickets/{{ ref | raw }}/Attachments',
            'parentRef'  => 'parentID',
        ],
        'TicketCategoryFieldDefaults' => [
            'parentPath' => 'TicketCategories/{{ ref | raw }}/FieldDefaults',
            'parentRef'  => 'ticketCategoryID',
        ],
        'TicketChangeRequestApprovals' => [
            'parentPath' => 'Tickets/{{ ref | raw }}/ChangeRequestApprovals',
            'parentRef'  => 'ticketID',
        ],
        'TicketCharges' => [
            'parentPath' => 'Tickets/{{ ref | raw }}/Charges',
            'parentRef'  => 'ticketID',
        ],
        'TicketChecklistItems' => [
            'parentPath' => 'Tickets/{{ ref | raw }}/ChecklistItems',
            'parentRef'  => 'ticketID',
        ],
        'TicketChecklistLibraries' => [
            'parentPath' => 'Tickets/{{ ref | raw }}/ChecklistLibraries',
            'parentRef'  => 'ticketID',
        ],
        'TicketNotes' => [
            'parentPath' => 'Tickets/{{ ref | raw }}/Notes',
            'parentRef'  => 'ticketID',
        ],
        'TicketRmaCredits' => [
            'parentPath' => 'Tickets/{{ ref | raw }}/RmaCredits',
            'parentRef'  => 'ticketID',
        ],
        'TicketSecondaryResources' => [
            'parentPath' => 'Tickets/{{ ref | raw }}/SecondaryResources',
            'parentRef'  => 'ticketID',
        ],
        'UserDefinedFieldListItems' => [
            'parentPath' => 'UserDefinedFields/{{ ref | raw }}/ListItems',
            'parentRef'  => 'udfFieldID',
        ],
    ];

    /** @var array If the studdly caps being with these weird words we have to manually map to a lowercase version. */
    protected array $weirdWords = [
        'RMM' => 'rmm',
        'SIC' => 'sic',
    ];

    /**
     * Sets up the class.
     * 
     * @param  Client       $client  An HTTP client for interacting with Autotask.
     * @param  Environment  $twig    Twig engine for using templates.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function __construct(Client $client, Environment $twig)
    {
        $this->client = $client;
        parent::__construct($twig);
    }

    /**
     * Sets the endpoint we should interact with.
     * 
     * @param  string  $endpoint  The endpoint we are interacting with.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function setEndpoint(string $endpoint): void
    {
        $this->endpoint = new EndpointDataTransferObject([
            'plural'   => Str::pluralStudly($endpoint),
            'singular' => Str::singular($endpoint),
        ]);
    }

    /**
     * Creates all the files related to an endpoint.
     * 
     * @param  bool  $overwrite  Whether or not previous files should be overwritten.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function make(): void
    {
        if (! isset($this->endpoint)) {
            throw new Exception('Please set an endpoint!');
        }

        // Modify write settings
        // $this->setOutputDirectory( $this->outputDirectory );
        $this->setSubDirectory(File::endpointDirectory($this->endpoint));

        // Now write the files
        $this->makeCollection();
        $this->makeDataTransferObject();
        $this->makeService();
    }

    public function makeCollection(): void
    {
        /**
         * Step 1. Check the file
         */
        $filename = File::collectionFilename($this->endpoint);

        if (!$this->shouldWriteFile($filename)) {
            return;
        }

        /**
         * Step 2. Write the information to a file
         */
        $this->writeTemplate(
            'Collection.twig',
            $filename,
            [
                'endpoint'  => $this->endpoint,
            ]
        ); 
    }

    /**
     * Writes a Data Transfer Object file.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function makeDataTransferObject(): void
    {
        /**
         * Step 0. Reset stuff
         */
        $this->dtoFields = [];
        $this->dtoImports = [
            'GuzzleHttp\Psr7\Response',
            'Spatie\DataTransferObject\DataTransferObject',
        ];

        /**
         * Step 1. Check the file
         */
        $filename = File::dataTransferObjectFilename($this->endpoint);
        
        if (! $this->shouldWriteFile($filename)) {
            return;
        }

        /**
         * Step 2. Collect information
         */
        $httpResponse   = $this->client->get($this->endpoint->plural . "/entityInformation/fields");
        $arrayResponse  = json_decode($httpResponse->getBody(), true);

        if (!isset($arrayResponse['fields'])) {
            throw new Exception('Invalid response from entityInformation/fields!');
        }

        // Now convert all our fields into something we will understand
        foreach ($arrayResponse['fields'] as $field) {
            // This handles one-off scenarios where the field begins with an all
            // uppercase word (e.g. RMM).
            foreach ($this->weirdWords as $original => $fixed) {
                if (substr($field['name'], 0, strlen($original)) === $original) {
                    $field['name'] = Str::replaceFirst($original, $fixed, $field['name']);
                }
            }

            $this->dtoFields[] = [
                'name'      => Str::camel($field['name']),
                'type'      => $this->mapType($field['dataType']),
                'required'  => $field['isRequired'],
            ];
        }

        /**
         * Step 3. Write the information to a file
         */
        $this->writeTemplate(
            'Entity.twig',
            $filename,
            [
                'endpoint'  => $this->endpoint,
                'fields'    => $this->dtoFields,
                'imports'   => $this->dtoImports,
            ]
        );
    }

    /**
     * Creates a paginator class for the endpoint.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function makePaginator(): void
    {
        /**
         * Step 1. Check the file
         */
        $filename = File::paginatorFilename($this->endpoint);

        if (!$this->shouldWriteFile($filename)) {
            return;
        }

        /**
         * Step 2. Write the information to a file
         */
        $this->writeTemplate(
            'Paginator.twig',
            $filename,
            [
                'endpoint'  => $this->endpoint,
            ]
        );
    }

    /**
     * Creates a QueryBuilder class for the endpoint.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function makeQueryBuilder(): void
    {
        /**
         * Step 1. Check the file
         */
        $filename = File::queryBuilderFilename($this->endpoint);

        if (!$this->shouldWriteFile($filename)) {
            return;
        }

        /**
         * Step 2. Write the information to a file
         */
        $this->writeTemplate(
            'QueryBuilder.twig',
            $filename,
            [
                'endpoint'  => $this->endpoint,
            ]
        );
    }

    /**
     * Writes a service file.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function makeService()
    {
        /**
         * Step 1. Check the file
         */
        $filename = File::serviceFilename($this->endpoint);

        if (!$this->shouldWriteFile($filename)) {
            return;
        }

        /**
         * Step 2. Collect information
         */
        $httpResponse   = $this->client->get($this->endpoint->plural . "/entityInformation");
        $arrayResponse  = json_decode($httpResponse->getBody(), true);

        if (!isset($arrayResponse['info'])) {
            throw new Exception('Invalid response from entityInformation!');
        }

        $resource = new EntityInformationDataTransferObject([
            'canBeCreated' => $arrayResponse['info']['canCreate'],
            'canBeDeleted' => $arrayResponse['info']['canDelete'],
            'canBeQueried' => $arrayResponse['info']['canQuery'],
            'canBeUpdated' => $arrayResponse['info']['canUpdate'],
            'hasUserDefinedFields' => $arrayResponse['info']['hasUserDefinedFields'],
        ]);

        /**
         * Step 3. Check if it's a weird endpoint
         */
        if (isset($this->weirdEndpoints[$this->endpoint->plural])) {
            $endpointFix = $this->weirdEndpoints[$this->endpoint->plural]['parentPath'];
            $endpointRef = $this->weirdEndpoints[$this->endpoint->plural]['parentRef'];

            $template = $this->twig->createTemplate(
                $endpointFix
            );
            $endpointFix = $template->render(['ref' => '$' . $endpointRef]);
        }

        /**
         * Step 4. Write the information to a file
         */
        $this->writeTemplate(
            'Service.twig',
            $filename,
            [
                'endpoint'      => $this->endpoint,
                'imports'       => $this->serviceImports,
                'parentPath'    => $endpointFix ?? false,
                'parentRef'     => $endpointRef ?? false,
                'resource'      => $resource,
            ]
        );

        // $this->setSubDirectory(File::testDirectory($this->endpoint));
        // $this->writeTemplate(
        //     'ServiceTest.twig',
        //     File::serviceTestFilename($this->endpoint),
        //     [
        //         'endpoint'  => $this->endpoint,
        //         'resource'  => $resource
        //     ]
        // );

        /**
         * Step 4. Kick off other generators if applicable.
         */
        if ($resource->canBeQueried) {
            $this->makePaginator();
            $this->makeQueryBuilder();
        }
    }

    /**
     * This helper method converts the Autotask type to a PHP type.
     * 
     * @param  string  $type  The Autotask type to be converted.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    protected function mapType(string $type)
    {
        switch ($type) {
            case 'datetime':
                if (!in_array('Carbon\\Carbon', $this->dtoImports)) {
                    $this->dtoImports[] = 'Carbon\\Carbon';
                }
                return 'Carbon';
                break;
            case 'integer':
                return 'int';
                break;
            case 'long':
            case 'short':
                return 'int';
                break;
            case 'boolean':
                return 'bool';
                break;
            case 'double':
            case 'decimal':
                return 'float';
                break;
            default:
                return $type;
                break;
        }
    }
}
