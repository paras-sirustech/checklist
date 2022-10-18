<template>
    <card class="flex flex-col items-center justify-center">
        <div class="px-3 py-3">
            <h1 class="text-center text-3xl text-80 font-light">Escalation Matrix/ SLA</h1>
        </div>
        <div class="row">
            <div class="col-md-8 offset-2">
                <table class="table table-striped">
                    <colgroup>
                        <col width="30%">
                        <col width="40%">
                        <col width="30%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>SLA Stage</th>
                            <th>P1</th>
                            <th>P2</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Issue Reported</td>
                            <td>Email notification to the initiator, shop manager, assigned support team member, Suhail Al Shahbal</td>
                            <td>Email notification to the initiator, shop manager, assigned support team member, Suhail Al Shahbal</td>
                        </tr>
                        <tr>
                            <td>No action/resolution within 1 Hr</td>
                            <td>Reminder sent to the support team member</td>
                            <td>N/A</td>
                        </tr>
                        <tr>
                            <td>No action/resolution within 2 Hrs</td>
                            <td>Escalated to Suhail Al Shahbal, Shop Manager</td>
                            <td>N/A</td>
                        </tr>
                        <tr>
                            <td>No action/resolution within 4 Hrs</td>
                            <td>Escalated to Kareem Al Mansy, Cluster Manager</td>
                            <td>N/A</td>
                        </tr>
                        <tr>
                            <td>No action/resolution within 24 Hr</td>
                            <td>N/A</td>
                            <td>Reminder sent to the support team member</td>
                        </tr>
                        <tr>
                            <td>No action/resolution within 36 Hrs</td>
                            <td>N/A</td>
                            <td>Escalated to Suhail Al Shahbal, Shop Manager</td>
                        </tr>
                        <tr>
                            <td>No action/resolution within 60 Hrs</td>
                            <td>N/A</td>
                            <td>Escalated to Kareem Al Mansy, Cluster Manager</td>
                        </tr>
                        <tr>
                            <td>Reolved the issue</td>
                            <td>Notification to initiator, Shop Manager, assigned support member, Suhail Al Shahbal (Kareem & CM in case it was escalated to their level)</td>
                            <td>Notification to initiator, Shop Manager, assigned support member, Suhail Al Shahbal (Kareem & CM in case it was escalated to their level)</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </card>
</template>

<script>
export default {
    props: [
        'card',

        // The following props are only available on resource detail cards...
        // 'resource',
        // 'resourceId',
        // 'resourceName',
    ],

    mounted() {
        //
    },
}
</script>
