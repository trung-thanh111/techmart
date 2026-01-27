export interface IPopupImage {
    path?: string | null;
    url?: string | null;
}

export interface IPopup {
    id?: number;
    title?: string | null;
    slug?: string | null;
    type?: string | null;
    content?: string | null;
    image?: IPopupImage | null;
    link?: string | null;
    buttonText?: string | null;
    formHtml?: string | null;
    showDelay?: number | null;
    show_delay?: number | null;
    priority?: boolean;
    publish?: number;
    createdAt?: string | null;
    updatedAt?: string | null;
}
