//interface base cho tất cả các response trả về từ backend
interface BaseInterface {
    status: boolean,
    code: number,
    message: string,
    timestamp: string,
}

// trường hợp response thành công -> có data trả về + base interface
export interface IApiOk<T> extends BaseInterface {
    data: T
}

// trường hợp response lỗi -> có error trả về + base interface
export interface IApiError<E> extends BaseInterface {
    error: E
}

//trường hợp validate data 
export type IUnprocessableEntity<T> = {
    [key in keyof T]: string[]
}

export interface IApiMessage extends BaseInterface {

}

//trường hợp response lỗi hoặc message hoặc thành công
export type IApiResponse<T, E> = IApiOk<T> | IApiError<E> | IApiMessage

//trường hợp response lỗi hoặc message
export type IApiErrorOrMessage<E> = IApiError<E> | IApiMessage

//link cho phân trang
export type TLink = {
    url: null | string,
    label: string,
    active: boolean,
}

//interface phân trang
export interface IPaginate<T>{
    current_page: number,
    data: T[],
    first_page_url: string,
    from: number,
    last_page: number,
    last_page_url: string,
    links: TLink[],
    next_page_url: string,
    path: string,
    per_page: number,
    prev_page_url: string,
    to: number,
    total: number,
}