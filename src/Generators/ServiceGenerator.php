<?php

namespace Anteris\Autotask\Generator\Generators;

use Anteris\Autotask\Generator\Responses\EntityInformation\EntityInformationDTO;
use Anteris\Autotask\Generator\Support\Entities\EntityNameDTO;
use Anteris\Autotask\Generator\Writers\TemplateWriter;

/**
 * This class is in charge off all file generation for Service classes.
 */
class ServiceGenerator
{
    /**
     * @var  array This lengthy and annoying array helps us define correct create / update routes for pesky nested resources.
     * @todo Make this less awkward in the code, maybe automatically generated later on.
     */
    protected array $weirdEndpoints = [
        'ChecklistLibraryChecklistItems' => [
            'path' => 'ChecklistLibraries/{{ ref | raw }}/ChecklistItems',
            'ref'  => 'checklistLibraryID',
        ],
        'CompanyAlerts' => [
            'path' => 'Companies/{{ ref | raw }}/Alerts',
            'ref'  => 'companyID',
        ],
        'CompanyAttachments' => [
            'path' => 'Companies/{{ ref | raw }}/Attachments',
            'ref'  => 'parentID',
        ],
        'Contacts' => [
            'path' => 'Companies/{{ ref | raw }}/Contacts',
            'ref'  => 'companyID',
        ],
        'CompanyLocations' => [
            'path' => 'Companies/{{ ref | raw }}/Locations',
            'ref'  => 'companyID',
        ],
        'CompanyNotes' => [
            'path' => 'Companies/{{ ref | raw }}/Notes',
            'ref'  => 'companyID',
        ],
        'CompanySiteConfigurations' => [
            'path' => 'Companies/{{ ref | raw }}/SiteConfigurations',
            'ref'  => 'companyID',
        ],
        'CompanyTeams' => [
            'path' => 'Companies/{{ ref | raw }}/Teams',
            'ref'  => 'companyID',
        ],
        'CompanyToDos' => [
            'path' => 'Companies/{{ ref | raw }}/ToDos',
            'ref'  => 'companyID',
        ],
        'CompanyWebhookExcludedResources' => [
            'path' => 'CompanyWebhooks/{{ ref | raw }}/ExcludedResources',
            'ref'  => 'resourceID',
        ],
        'CompanyWebhookFields' => [
            'path' => 'CompanyWebhooks/{{ ref | raw }}/Fields',
            'ref'  => 'webhookID',
        ],
        'CompanyWebhookUdfFields' => [
            'path' => 'CompanyWebhooks/{{ ref | raw }}/UdfFields',
            'ref'  => 'webhookID',
        ],
        'ConfigurationItemBillingProductAssociations' => [
            'path' => 'ConfigurationItems/{{ ref | raw }}/BillingProductAssociations',
            'ref'  => 'configurationItemID',
        ],
        'ConfigurationItemCategoryUdfAssociations' => [
            'path' => 'ConfigurationItemCategories/{{ ref | raw }}/UdfAssociations',
            'ref'  => 'configurationItemCategoryID',
        ],
        'ConfigurationItemNotes' => [
            'path' => 'ConfigurationItems/{{ ref | raw }}/Notes',
            'ref'  => 'configurationItemID',
        ],
        'ContactBillingProductAssociations' => [
            'path' => 'Contacts/{{ ref | raw }}/BillingProductAssociations',
            'ref'  => 'contactID',
        ],
        'ContactGroupContacts' => [
            'path' => 'ContactGroups/{{ ref | raw }}/Contacts',
            'ref'  => 'contactGroupID',
        ],
        'ContactWebhookExcludedResources' => [
            'path' => 'ContactWebhooks/{{ ref | raw }}/ExcludedResources',
            'ref'  => 'resourceID',
        ],
        'ContactWebhookFields' => [
            'path' => 'ContactWebhooks/{{ ref | raw }}/Fields',
            'ref'  => 'webhookID',
        ],
        'ContactWebhookUdfFields' => [
            'path' => 'ContactWebhooks/{{ ref | raw }}/UdfFields',
            'ref'  => 'webhookID',
        ],
        'ContractBillingRules' => [
            'path' => 'Contracts/{{ ref | raw }}/BillingRules',
            'ref'  => 'contractID',
        ],
        'ContractBlockHourFactors' => [
            'path' => 'Contracts/{{ ref | raw }}/BlockHourFactors',
            'ref'  => 'contractID',
        ],
        'ContractBlocks' => [
            'path' => 'Contracts/{{ ref | raw }}/Blocks',
            'ref'  => 'contractID',
        ],
        'ContractCharges' => [
            'path' => 'Contracts/{{ ref | raw }}/Charges',
            'ref'  => 'contractID',
        ],
        'ContractExclusionBillingCodes' => [
            'path' => 'Contracts/{{ ref | raw }}/ExclusionBillingCodes',
            'ref'  => 'contractID',
        ],
        'ContractExclusionRoles' => [
            'path' => 'Contracts/{{ ref | raw }}/ExclusionRoles',
            'ref'  => 'contractID',
        ],
        'ContractExclusionSetExcludedRoles' => [
            'path' => 'ContractExlusionSets/{{ ref | raw }}/ExcludedRoles',
            'ref'  => 'contractExlusionSetID',
        ],
        'ContractExclusionSetExcludedWorkTypes' => [
            'path' => 'ContractExlusionSets/{{ ref | raw }}/ExcludedWorkTypes',
            'ref'  => 'contractExlusionSetID',
        ],
        'ContractMilestones' => [
            'path' => 'Contracts/{{ ref | raw }}/Milestones',
            'ref'  => 'contractID',
        ],
        'ContractNotes' => [
            'path' => 'Contracts/{{ ref | raw }}/Notes',
            'ref'  => 'contractID',
        ],
        'ContractRates' => [
            'path' => 'Contracts/{{ ref | raw }}/Rates',
            'ref'  => 'contractID',
        ],
        'ContractRetainers' => [
            'path' => 'Contracts/{{ ref | raw }}/Retainers',
            'ref'  => 'contractID',
        ],
        'ContractRoleCosts' => [
            'path' => 'Contracts/{{ ref | raw }}/RoleCosts',
            'ref'  => 'contractID',
        ],
        'ContractServiceAdjustments' => [
            'path' => 'Contracts/{{ ref | raw }}/ServiceAdjustments',
            'ref'  => 'contractID',
        ],
        'ContractServiceBundleAdjustments' => [
            'path' => 'Contracts/{{ ref | raw }}/ServiceBundleAdjustments',
            'ref'  => 'contractID',
        ],
        'ContractServiceBundles' => [
            'path' => 'Contracts/{{ ref | raw }}/ServiceBundles',
            'ref'  => 'contractID',
        ],
        'ContractServiceBundleUnits' => [
            'path' => 'Contracts/{{ ref | raw }}/ServiceBundleUnits',
            'ref'  => 'contractID',
        ],
        'ContractServices' => [
            'path' => 'Contracts/{{ ref | raw }}/Services',
            'ref'  => 'contractID',
        ],
        'ContractServiceUnits' => [
            'path' => 'Contracts/{{ ref | raw }}/ServiceUnits',
            'ref'  => 'contractID',
        ],
        'ContractTicketPurchases' => [
            'path' => 'Contracts/{{ ref | raw }}/TicketPurchases',
            'ref'  => 'contractID',
        ],
        'ExpenseItems' => [
            'path' => 'Expenses/{{ ref | raw }}/Items',
            'ref'  => 'expenseReportID',
        ],
        'Holidays' => [
            'path' => 'HolidaySets/{{ ref | raw }}/Holidays',
            'ref'  => 'holidaySetID',
        ],
        'InventoryItemSerialNumbers' => [
            'path' => 'InventoryItems/{{ ref | raw }}/SerialNumbers',
            'ref'  => 'inventoryItemID',
        ],
        'OpportunityAttachments' => [
            'path' => 'Opportunities/{{ ref | raw }}/Attachments',
            'ref'  => 'opportunityID',
        ],
        'OrganizationalResources' => [
            'path' => 'OrganizationalLevelAssociations/{{ ref | raw }}/Resources',
            'ref'  => 'organizationalLevelAssociationID',
        ],
        'Phases' => [
            'path' => 'Projects/{{ ref | raw }}/Phases',
            'ref'  => 'projectID',
        ],
        'ProductNotes' => [
            'path' => 'Products/{{ ref | raw }}/Notes',
            'ref'  => 'productID',
        ],
        'ProductTiers' => [
            'path' => 'Products/{{ ref | raw }}/Tiers',
            'ref'  => 'productID',
        ],
        'ProductVendors' => [
            'path' => 'Products/{{ ref | raw }}/Vendors',
            'ref'  => 'productID',
        ],
        'ProjectAttachments' => [
            'path' => 'Projects/{{ ref | raw }}/Attachments',
            'ref'  => 'parentID',
        ],
        'ProjectCharges' => [
            'path' => 'Projects/{{ ref | raw }}/Charges',
            'ref'  => 'projectID',
        ],
        'ProjectNotes' => [
            'path' => 'Projects/{{ ref | raw }}/Notes',
            'ref'  => 'projectID',
        ],
        'PurchaseOrderItemReceiving' => [
            'path' => 'PurchaseOrderItems/{{ ref | raw }}/Receiving',
            'ref'  => 'purchaseOrderItemID',
        ],
        'PurchaseOrderItems' => [
            'path' => 'PurchaseOrders/{{ ref | raw }}/Items',
            'ref'  => 'orderID',
        ],
        'QuoteItems' => [
            'path' => 'Quotes/{{ ref | raw }}/Items',
            'ref'  => 'quoteID',
        ],
        'ResourceRoleDepartments' => [
            'path' => 'Resources/{{ ref | raw }}/RoleDepartments',
            'ref'  => 'resourceID',
        ],
        'ResourceRoleQueues' => [
            'path' => 'Resources/{{ ref | raw }}/RoleQueues',
            'ref'  => 'resourceID',
        ],
        'ResourceRoles' => [
            'path' => 'Resources/{{ ref | raw }}/Roles',
            'ref'  => 'resourceID',
        ],
        'ResourceServiceDeskRoles' => [
            'path' => 'Resources/{{ ref | raw }}/ServiceDeskRoles',
            'ref'  => 'resourceID',
        ],
        'ResourceSkills' => [
            'path' => 'Resources/{{ ref | raw }}/Skills',
            'ref'  => 'resourceID',
        ],
        'SalesOrder' => [
            'path' => 'Opportunities/{{ ref | raw }}/SalesOrders',
            'ref'  => 'opportunityID',
        ],
        'ServiceBundleServices' => [
            'path' => 'ServiceBundles/{{ ref | raw }}/Services',
            'ref'  => 'serviceBundleID',
        ],
        'ServiceCallTaskResources' => [
            'path' => 'ServiceCallTasks/{{ ref | raw }}/Resources',
            'ref'  => 'serviceCallTaskID',
        ],
        'ServiceCallTasks' => [
            'path' => 'ServiceCalls/{{ ref | raw }}/Tasks',
            'ref'  => 'serviceCallID',
        ],
        'ServiceCallTicketResources' => [
            'path' => 'ServiceCallTickets/{{ ref | raw }}/Resources',
            'ref'  => 'serviceCallTicketID',
        ],
        'ServiceCallTickets' => [
            'path' => 'ServiceCalls/{{ ref | raw }}/Tickets',
            'ref'  => 'serviceCallID',
        ],
        'ServiceLevelAgreementResults' => [
            'path' => 'ServiceLevelAgreements/{{ ref | raw }}/Results',
            'ref'  => 'serviceCallTicketID',
        ],
        'SubscriptionPeriods' => [
            'path' => 'Subscriptions/{{ ref | raw }}/Periods',
            'ref'  => 'subscriptionID',
        ],
        'TaskAttachments' => [
            'path' => 'Tasks/{{ ref | raw }}/Attachments',
            'ref'  => 'parentID',
        ],
        'TaskNotes' => [
            'path' => 'Tasks/{{ ref | raw }}/Notes',
            'ref'  => 'taskID',
        ],
        'TaskPredecessors' => [
            'path' => 'Tasks/{{ ref | raw }}/Predecessors',
            'ref'  => 'taskID',
        ],
        'Tasks' => [
            'path' => 'Projects/{{ ref | raw }}/Tasks',
            'ref'  => 'projectID',
        ],
        'TaskSecondaryResources' => [
            'path' => 'Tasks/{{ ref | raw }}/SecondaryResources',
            'ref'  => 'taskID',
        ],
        'TicketAdditionalConfigurationItems' => [
            'path' => 'Tickets/{{ ref | raw }}/AdditionalConfigurationItems',
            'ref'  => 'ticketID',
        ],
        'TicketAdditionalContacts' => [
            'path' => 'Tickets/{{ ref | raw }}/AdditionalContacts',
            'ref'  => 'ticketID',
        ],
        'TicketAttachments' => [
            'path' => 'Tickets/{{ ref | raw }}/Attachments',
            'ref'  => 'parentID',
        ],
        'TicketCategoryFieldDefaults' => [
            'path' => 'TicketCategories/{{ ref | raw }}/FieldDefaults',
            'ref'  => 'ticketCategoryID',
        ],
        'TicketChangeRequestApprovals' => [
            'path' => 'Tickets/{{ ref | raw }}/ChangeRequestApprovals',
            'ref'  => 'ticketID',
        ],
        'TicketCharges' => [
            'path' => 'Tickets/{{ ref | raw }}/Charges',
            'ref'  => 'ticketID',
        ],
        'TicketChecklistItems' => [
            'path' => 'Tickets/{{ ref | raw }}/ChecklistItems',
            'ref'  => 'ticketID',
        ],
        'TicketChecklistLibraries' => [
            'path' => 'Tickets/{{ ref | raw }}/ChecklistLibraries',
            'ref'  => 'ticketID',
        ],
        'TicketNotes' => [
            'path' => 'Tickets/{{ ref | raw }}/Notes',
            'ref'  => 'ticketID',
        ],
        'TicketRmaCredits' => [
            'path' => 'Tickets/{{ ref | raw }}/RmaCredits',
            'ref'  => 'ticketID',
        ],
        'TicketSecondaryResources' => [
            'path' => 'Tickets/{{ ref | raw }}/SecondaryResources',
            'ref'  => 'ticketID',
        ],
        'UserDefinedFieldListItems' => [
            'path' => 'UserDefinedFields/{{ ref | raw }}/ListItems',
            'ref'  => 'udfFieldID',
        ],
    ];

    /** @var TemplateWriter The interface with which we will write new files. */
    protected $writer;

    /**
     * Sets up the class to begin generating files.
     */
    public function __construct(TemplateWriter $writer)
    {
        $this->writer = $writer;
    }

    /**
     * This handles child endpoints that need to have specific parent paths
     * for creating / updating resources.
     *
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    protected function getParentInfo(EntityNameDTO $entityName): array
    {
        $array = [
            'path' => false,
            'ref'  => false,
        ];

        if (isset($this->weirdEndpoints[$entityName->plural])) {
            $array         = $this->weirdEndpoints[$entityName->plural];
            $array['path'] = str_replace(
                '{{ ref | raw }}',
                '$' . $array['ref'],
                $array['path']
            );
        }

        return $array;
    }

    /**
     * Creates a new service class from the information passed.
     *
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function make(
        EntityNameDTO $entityName,
        EntityInformationDTO $entityInformation
    ): void {
        $this->writer->createFileFromTemplate(
            $entityName->singular . 'Service.php',
            'Package/API/Service.php.twig',
            [
                'entityName'        => $entityName,
                'entityInformation' => $entityInformation,
                'parent'            => $this->getParentInfo($entityName),
            ]
        );

        // Call the other generators that this one is dependent on
        if ($entityInformation->canQuery) {
            $queryBuilderGenerator = new QueryBuilderGenerator($this->writer);
            $queryBuilderGenerator->make($entityName);
        }

        $serviceTestGenerator = new ServiceTestGenerator($this->writer->newContext());
        $serviceTestGenerator->make($entityName, $entityInformation);
    }
}
