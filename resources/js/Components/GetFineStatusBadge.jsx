import { Badge } from '@/Components/ui/badge';

export default function GetFineStatusBadge({ status }) {
    // status resource shape { value, label }
    const value = status.value;
    const label = status.label;

    const styles = {
        tertunda: 'text-white bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-500 border-yellow-500',
        sukses: 'text-white bg-gradient-to-r from-green-400 via-green-500 to-green-500 border-green-500',
        gagal: 'text-white bg-gradient-to-r from-red-400 via-red-500 to-red-500 border-red-500',
    };

    const className = styles[value] ?? '';

    return <Badge className={className}>{label ?? '-'}</Badge>;
}
