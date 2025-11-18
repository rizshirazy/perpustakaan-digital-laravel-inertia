import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from '@/Components/ui/command';
import { Popover, PopoverContent, PopoverTrigger } from '@/Components/ui/popover';
import { cn } from '@/lib/utils';
import { IconCheck, IconChevronDown } from '@tabler/icons-react';
import { useMemo, useState } from 'react';

export default function ComboBox({
    items = [],
    selectedItem,
    onSelect,
    placeholder = 'Pilih item...',
    emptyText = 'Item tidak ditemukan',
    className,
}) {
    const [open, setOpen] = useState(false);

    const selectedOption = useMemo(() => {
        return items.find((item) => String(item.value) === String(selectedItem));
    }, [items, selectedItem]);

    const handleSelect = (value) => {
        onSelect(value);
        setOpen(false);
    };

    return (
        <Popover open={open} onOpenChange={setOpen}>
            <PopoverTrigger asChild>
                <button
                    type="button"
                    role="combobox"
                    aria-expanded={open}
                    className={cn(
                        'flex h-12 w-full items-center justify-between rounded-md border border-input bg-transparent px-3 py-2 text-sm text-foreground shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-orange-500 disabled:cursor-not-allowed disabled:opacity-50',
                        !selectedOption && 'text-muted-foreground',
                        className,
                    )}
                >
                    <span className="line-clamp-1 text-left">{selectedOption?.label ?? placeholder}</span>
                    <IconChevronDown className="ml-2 h-4 w-4 shrink-0 opacity-50" />
                </button>
            </PopoverTrigger>
            <PopoverContent
                className="max-h-[--radix-popover-content-available-height] w-[--radix-popover-trigger-width] p-0"
                align="start"
            >
                <Command>
                    <CommandInput placeholder={placeholder} className="h-9 text-sm" />
                    <CommandList>
                        <CommandEmpty>{emptyText}</CommandEmpty>
                        <CommandGroup>
                            {items.map((item, index) => (
                                <CommandItem
                                    key={index}
                                    value={`${item.label} ${item.value}`}
                                    onSelect={() => handleSelect(item.value)}
                                >
                                    <span className="line-clamp-1">{item.label}</span>
                                    <IconCheck
                                        className={cn(
                                            'ml-auto h-4 w-4 text-orange-500',
                                            String(selectedItem) === String(item.value) ? 'opacity-100' : 'opacity-0',
                                        )}
                                    />
                                </CommandItem>
                            ))}
                        </CommandGroup>
                    </CommandList>
                </Command>
            </PopoverContent>
        </Popover>
    );
}
