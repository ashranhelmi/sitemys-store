// sitemys. store lockup (Sora wordmark + red dot), same as the storefront partial.
export default function Wordmark({ size = 26, label = 'store' }) {
    return (
        <span className="inline-flex flex-col leading-none select-none">
            {label && <span className="text-[9px] font-bold uppercase tracking-[0.32em] text-muted-foreground mb-0.5">{label}</span>}
            <span className="font-display tracking-[-0.035em] text-foreground inline-flex items-baseline" style={{ fontSize: size }}>
                sitemys<span className="inline-block rounded-full bg-[#E0312B] dark:bg-[#FF4A3D]" style={{ width: '0.2em', height: '0.2em', marginLeft: '0.04em' }} />
            </span>
        </span>
    );
}
