<!DOCTYPE html>
<html>
<head>
    <title>Xero Dashboard</title>
</head>
<body>
    <h1>Welcome to your Xero Dashboard</h1>

    <h2>Organization</h2>
    <p><strong>Name:</strong> {{ $org->getName() }}</p>
    <p><strong>Legal Name:</strong> {{ $org->getLegalName() }}</p>
    <p><strong>Country:</strong> {{ $org->getCountryCode() }}</p>

    <h2>Recent Invoices</h2>
    <ul>
        @foreach ($invoices as $invoice)
            <li>{{ $invoice->getContact()->getName() }} - {{ $invoice->getTotal() }} {{ $invoice->getCurrencyCode() }}</li>
        @endforeach
    </ul>

    <h2>Recent Contacts</h2>
    <ul>
        @foreach ($contacts as $contact)
            <li>{{ $contact->getName() }} - {{ $contact->getEmailAddress() }}</li>
        @endforeach
    </ul>

    <h2>Bank Accounts</h2>
    <ul>
        @foreach ($bankAccounts as $account)
            <li>{{ $account->getName() }} ({{ $account->getBankAccountNumber() }})</li>
        @endforeach
    </ul>
</body>
</html>
